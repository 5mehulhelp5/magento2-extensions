<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Depositpayment
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/deposit-payment-m2.html
*
**/
namespace Milople\Depositpayment\Controller\Index;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Displaytable extends \Magento\Framework\App\Action\Action
{
	 /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
	
	protected $productModel;

    protected $partialHelper;
	
	protected $dataHelper;
	
	protected $calculationModel;
	
	protected $currency;
	
	protected $storeManager;
	
	protected $partialBlock;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Catalog\Model\Product $productModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Milople\Depositpayment\Helper\Data $dataHelper,
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Milople\Depositpayment\Block\Partialpayment $partialBlock,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Directory\Model\Currency $currency
	)
    {
        $this->resultPageFactory = $resultPageFactory;
		$this->productModel = $productModel;
		$this->partialHelper = $partialHelper;
		$this->resultJsonFactory = $resultJsonFactory;
		$this->dataHelper = $dataHelper;
        $this->calculationModel =  $calculationModel;   
		$this->_storeManager = $storeManager;
		$this->partialBlock = $partialBlock;
		$this->cart = $cart;
		$this->storedate = $date;
		$this->_logger = $logger;
		$this->currency = $currency;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
	{
		$quote = $this->cart->getQuote()->getData();
		$imageUrl = $this->partialBlock->getViewFileUrl('Milople_Depositpayment::images');
		$data = $this->getRequest()->getPost();
		
		$productId = 0;
		$noOfInstallments = 0;

		if(isset($data['productId']) && $data['productId']!="")
		{
			$product = $this->productModel->load($data['productId']);
			$productId = $product->getId();
		}
		else
		{
			$product = NULL;
		}
		$price=$data['price'];
		$base_price = $this->partialHelper->convertToCurrentCurrencyAmount($price);
		$optional = $this->partialHelper->isAllowFullPayment();
		$quote = $this->calculationModel->getQuote();
		//$currency = $this->currency->getCurrencySymbol();
		$currency = $data['currencySymbol'];
		$isFlexyAllow = $this->partialHelper->isAllowFlexyPayment($product);
				
		if($product!=NULL)//if product page
		{
			if(isset($data['numOfInstallment']) && $data['numOfInstallment'])// if flexy with selected or all product
			{
				$noOfInstallments = $data['numOfInstallment'];
				$installmentAmount = $dpayment=number_format($this->calculationModel->getDownPaymentAmount($price, $product, $noOfInstallments), 2, '.', '');
				$remaining=$price - $dpayment;
			}
			elseif(!isset($data['numOfInstallment'])){ // if no flexy with selected or all product
				$noOfInstallments = $this->partialHelper->getTotalIinstallments($product);				
				$dpayment = number_format($this->calculationModel->getDownPaymentAmount($price,$product), 2, '.', '');
				/* echo gettype($price)." ".$price."<br>";
				echo gettype($dpayment)." ".$dpayment."<br>";
				echo gettype($noOfInstallments)." ".$noOfInstallments."<br>"; */
				
				$installmentAmount = (float)(((int)$price-(int)$dpayment)/($noOfInstallments-1));
				$remaining = (int)$price - (int)$dpayment;	
			}
		}
		else //cart page
		{
			$price = $quote->getBaseSubtotal();//$quote->getSubtotal();
			$base_price = $this->partialHelper->convertToCurrentCurrencyAmount($price);			
			
			if($isFlexyAllow){
				$noOfInstallments = $data['numOfInstallment'];
				$dpayment = $installmentAmount = number_format((float)($price/$noOfInstallments), 2, '.', '');
			}else{
				$noOfInstallments = $this->partialHelper->getTotalIinstallments($product);
				$dpayment = $this->partialHelper->convertToCurrentCurrencyAmount($this->calculationModel->getDownPaymentAmount($base_price));
				$installmentAmount = (float)(($price-$dpayment)/($noOfInstallments-1));
			}
			$remaining=$price-$dpayment;
		}
		$html= '<div id="close-table" onclick=fullpaymentChecked(0);><img src="'.$imageUrl.'/close.png" alt="close"></div>';
		$html .="<div class='white_content'>";
		if((($quote && $productId==0) || $product->getTypeID()!='grouped') && $noOfInstallments)
		{
			$html.= "<table class='partial-payment-calculation'>
						<thead>
							<tr>
								<td>Installment </td><td>Due Date</td>";
						
						if($productId==0)	// whole cart
						{
							$html.="<td>Amount </td></tr>";
						}
						elseif(isset($data['numOfInstallment'])){// if flexy with selected or all product
							$html.="<td>Amount <div id='refresh-only-table' onclick=installmentChecked(2,'".$data['productId']."','".$data['url']."','".$data['currencySymbol']."','".$data['currencyCode']."',1);><img src='$imageUrl/refresh.png' alt='refresh'></div></td></tr>";
						}
						else
						{	
							$html.="<td>Amount <div id='refresh-only-table' onclick=installmentChecked(2,'".$data['productId']."','".$data['url']."','".$data['currencySymbol']."','".$data['currencyCode']."');><img src='$imageUrl/refresh.png' alt='refresh'></div></td></tr>";
						}
					
				$html.="
				</thead>
				<tbody>
					<tr>
						<td style=text-align:center>1<sup>st</sup></td>
						<td style=text-align:center>";
						$html.= $this->calculationModel->calculateInstallmentDates(date('Y-m-d'),0); 
						$html.="</td><td>".$currency.number_format((float)$dpayment, 2, '.', '')."</td>
					</tr>";
					
					for ($x = 2; $x <= $noOfInstallments; $x++) 
					{
						if($noOfInstallments == $x){
							$lastInstallmentAmount = $currency.$this->partialHelper->adjustLastInstallment($remaining,$installmentAmount,$noOfInstallments);
						}else{
							$lastInstallmentAmount = $currency.number_format($installmentAmount, 2, '.', '');
						}
						$html.="<tr>
							<td style=text-align:center>";
							$html.= $this->partialHelper->addOrdinalNumberSuffix($x)."</td>
							<td style=text-align:center>".$this->calculationModel->calculateInstallmentDates(date('Y-m-d'),$x-1)."</td>
							<td >".$lastInstallmentAmount."</td>
							</tr>";
					}
					$html.="<tr class='total'>
						<td colspan=2>Total&nbsp;</td>
						<td>".$currency.number_format((float)$price, 2, '.', '')." </td>
					</tr>
					<tr class='totalpp'>
						<td colspan=2>Down Payment&nbsp;</td><td style='padding-bottom:2px;'>".$currency.number_format((float)$dpayment, 2, '.', '')." </td>
					</tr>
					<tr class='totalpp'>
						<td colspan=2>Amount to be Paid Later&nbsp;</td>
						<td style='padding-bottom:2px;'>".$currency.number_format((float)$remaining, 2, '.', '')."</td>
					</tr>
				</tbody>
			</table>";
		}
		else
		{
			if($this->partialHelper->getCalculateDownpaymentOn()==1)
			{
				$down_payment=$currency.number_format((float)$this->partialHelper->getDownPaymentValue(), 2, '.', '');
			}
			else
			{
				$down_payment = $this->partialHelper->getDownPaymentValue()."%";
			}
			$remaining_installments = 1; //$this->partialHelper->getTotaIinstallments()-1;
			if($this->partialHelper->getPaymentplan()==1)
			{
				$plan = "month";
			}
			else if($this->partialHelper->getPaymentplan()==2)
			{
				$plan = "week";
			}
			else
			{
				$plan = $this->partialHelper->getNumberOfDays()." days";
			}
			$html .= "<p class='note-data'>Down payment will be $down_payment of product price.</p>";
			$html .= "<p class='note-data'>Remaining amount will be distributed equally in $remaining_installments installments, to be paid every $plan.</p>";			
		}
		if($this->partialHelper->autocapture())
		{
			$html.="<p class='note-data'>We don't save your Credit Card details, you are secure.</p>";
			$html.="<p class='note-data'>Your Credit Card will be charged automatically.</p>";
		}
		$result = $this->resultJsonFactory->create();
		$html.="<p class='note-data'>Final amount may vary based on Selected Custom Options, Shipping Method, Tax and Discount.</p></div>";
		$result->setData(['html'   => $html]);	
		return $result; 
   }
}
