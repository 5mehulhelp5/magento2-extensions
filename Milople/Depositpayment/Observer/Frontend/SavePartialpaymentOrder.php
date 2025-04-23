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
namespace Milople\Depositpayment\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NotFoundException;
use Exception;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class SavePartialpaymentOrder implements ObserverInterface
{ 
	protected $messageManager;
	const HASH_SEPARATOR = ":::";
	const DB_DELIMITER = "\r\n";
	
    public function __construct (
		\Magento\Framework\AuthorizationInterface $authorization,
		\Magento\Customer\Model\Session $customersession,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localedate,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Backend\Model\Session\Quote $sessionQuote,
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Milople\Depositpayment\Helper\Data $dataHelper,
		\Milople\Depositpayment\Helper\EmailSender $emailSender,
		\Milople\Depositpayment\Model\PartialpaymentOrders $partialOrder,
		\Milople\Depositpayment\Model\PartialpaymentProducts $partialProducts,
		\Milople\Depositpayment\Model\PartialpaymentInstallments $partialInstallment,
		\PSr\Log\LoggerInterface $logger
		) {
        $this->_authorization = $authorization;
		$this->customersession = $customersession; 
		$this->localeDate = $localedate; 
		$this->storedate = $date;
		$this->logger=$logger;
		$this->sessionQuote = $sessionQuote;
		$this->products = $productModel;
		$this->partialpaymentHelper = $partialHelper;
		$this->paritalOrder=$partialOrder;
		$this->partialProducts=$partialProducts;
		$this->partialInstallment=$partialInstallment;
		$this->calculationModel = $calculationModel;
		$this->emailSender = $emailSender;
	}
 
    public function execute(Observer $observer)
    {
		$order = $observer->getOrder();
		$quoteId=$order->getQuoteId();		
		$this->sessionQuote->unsProductItem();//For Admin Order
		try
		{
			$totalAmount = $order->getBaseGrandTotal();
			$paid_amount = $order->getBasePaidAmount();
			$remaining_amount = $order->getBaseRemainingAmount();
			if($paid_amount > 0 && $remaining_amount > 0)
			{
				$total_installments = 0;
				$paid_installments = 1;
				$items = $order->getAllVisibleItems();				
				if($this->calculationModel->isAllowWholeCart()){					
					if($this->partialpaymentHelper->isAllowFlexyPayment()){						
						$total_installments = $this->calculationModel->getNumberOfInstallmentsForFlexy();
					}else{
						$total_installments = $this->partialpaymentHelper->getNumberOfInstallments();
					}
				}else{// All / Specific Products
					foreach($items as $item)
					{
						$product = $this->products->load($item->getProductId());
						if($this->calculationModel->isAllowPartialPaymentInQuoteItem($item)){//
							if($this->partialpaymentHelper->isAllowFlexyPayment($product) && $this->calculationModel->isAllowPartialPaymentInQuoteItem($item) > $total_installments)
							{
								$total_installments = $this->calculationModel->isAllowPartialPaymentInQuoteItem($item);
							}
							else if(!$this->partialpaymentHelper->isAllowFlexyPayment($product) && $this->partialpaymentHelper->getTotalIinstallments($product) > $total_installments)
							{
								$total_installments = $this->partialpaymentHelper->getTotalIinstallments($product);
							}
						}
					}
				}
				$remaining_installments = $total_installments - $paid_installments;
				# set main partial payment orders table data
				$partialPaymentOrders =$this->paritalOrder
				  	->setOrderId($order->getId())
					->setTotalInstallments($total_installments)
					->setPaidInstallments($paid_installments)
					->setRemainingInstallments($remaining_installments)
					->setTotalAmount($totalAmount)
					->setPaidAmount($paid_amount)
					->setRemainingAmount($remaining_amount)
					->save();
				$partialPaymentId = $partialPaymentOrders->getId();				
				
				# set product table data				
				foreach($items as $item)
				{
					$partialPaymentProducts = $this->partialProducts;
					$product = $this->products->load($item->getProductId());
					//$product_total_amount = $product_paid_amount = $item->getPrice() * $item->getQtyOrdered();
					$product_total_amount = $product_paid_amount = $item->getBaseRowTotal();
					$product_total_installments = $product_paid_installments = 1;
					$product_remaining_installments = $product_remaining_amount = 0;
					if(($this->partialpaymentHelper->isSelectedProducts() ||$this->partialpaymentHelper->isAllProducts()) &&  $this->calculationModel->isAllowPartialPaymentInQuoteItem($item))//if partial payment selected for product
					{						
						if($this->partialpaymentHelper->isAllowFlexyPayment($product)){// Flexy							
							$product_total_installments = $this->calculationModel->isAllowPartialPaymentInQuoteItem($item);
							$product_paid_amount = $this->calculationModel->getDownPaymentAmount($product_total_amount,$product,$product_total_installments);
						}else{// Multi
							$product_total_installments = $this->partialpaymentHelper->getTotalIinstallments($product);
							$product_paid_amount = $this->calculationModel->getDownPaymentAmount($product_total_amount,$product);
						}
						
					}
					elseif($this->calculationModel->isAllowWholeCart()){
						if($this->partialpaymentHelper->isAllowFlexyPayment()){
							$product_total_installments = $this->calculationModel->getNumberOfInstallmentsForFlexy();
							$product_paid_amount = $this->calculationModel->getDownPaymentAmount($product_total_amount,NULL,$product_total_installments);
						}else{
							$product_total_installments = $this->partialpaymentHelper->getNumberOfInstallments();
							$product_paid_amount = $this->calculationModel->getDownPaymentAmount($product_total_amount);
						}
					}					
					$product_remaining_installments = $product_total_installments - $product_paid_installments;
					$product_remaining_amount = $product_total_amount - $product_paid_amount ;
					$partialPaymentProducts
					->setPartialPaymentId($partialPaymentId)
					->setSalesFlatOrdersItemId($item->getId())
					->setTotalInstallments($product_total_installments)
					->setPaidInstallments($product_paid_installments)
					->setRemainingInstallments($product_remaining_installments)
					->setDownpayment(number_format($product_paid_amount, 2, '.', ''))
					->setTotalAmount(number_format($product_total_amount, 2, '.', ''))
					->setPaidAmount(number_format($product_paid_amount, 2, '.', ''))
					->setRemainingAmount(number_format($product_remaining_amount, 2, '.', ''))
					->save();
					$partialPaymentProducts->unsetData();	
				}
				# set Installment Data
				$totlCalcAmount = 0;
				$tax = $order->getBaseTaxAmount();
				$shipping_tax = (float) $order->getBaseShippingAmount() + $tax;
				$discount = abs($order->getBaseDiscountAmount());
				for($i=1;$i<=$total_installments;$i++)
				{	
					$partialPaymentInstallments = $this->partialInstallment;
					$date = $this->calculationModel->calculateInstallmentDates(date('Y-m-d'),$i-1);
					# Down Payment(First Installment)
					if($i==1)
					{		
						$partialPaymentInstallments->setData(array(
						'partial_payment_id'=>$partialPaymentId,
						'installment_amount'=>$paid_amount,
						'installment_paid_date'=>$date,
						'installment_status'=>"Paid",
						'payment_method'=>$order->getPayment()->getMethod(),
						'installment_due_date'=>$date
						))->save();
						$totlCalcAmount += (float)number_format($paid_amount, 2, '.', '');						
					}
					else
					{
						$installmentAmount = 0;
						if($i == $total_installments){							
							$installmentAmount = $totalAmount - $totlCalcAmount;
						}else{
							if($this->calculationModel->isAllowWholeCart())
							{
								$installmentAmount = $order->getBaseRemainingAmount()/($total_installments-1);
							}else
							{								
								foreach($items as $item)
								{
									$product = $this->products->load($item->getProductId());
									$price = $item->getBaseRowTotal();
									if($this->calculationModel->isAllowPartialPaymentInQuoteItem($item)){
										if($this->partialpaymentHelper->isAllowFlexyPayment($product) && $i <= $this->calculationModel->isAllowPartialPaymentInQuoteItem($item)){
											
											$installmentAmount += $price/$this->calculationModel->isAllowPartialPaymentInQuoteItem($item);
										}elseif(!$this->partialpaymentHelper->isAllowFlexyPayment($product) && $i<=$this->partialpaymentHelper->getTotalIinstallments($product)){
											
											$downPayment = $this->calculationModel->getDownPaymentAmount($price,$product);
											$installmentAmount += ($price-$downPayment) / ($this->partialpaymentHelper->getTotalIinstallments($product)-1);
										}
									}
								}
								if(!$this->partialpaymentHelper->isShippingAndTaxInPayingNow()){
									
									$installmentAmount +=  $shipping_tax/($total_installments-1);
								}
								if(!$this->partialpaymentHelper->isDiscountInPayingNow()){
									$installmentAmount +=  $discount/($total_installments-1);
								}
								if($this->partialpaymentHelper->installmentFeeStatus() && $this->partialpaymentHelper->installmentFeeStatus() == 2 && $order->getBaseInstallmentFee()){
									$installmentAmount += $order->getBaseInstallmentFee()/$total_installments;
								}	
							}
							$totlCalcAmount += (float)number_format($installmentAmount, 2, '.', '');	
						}
						$partialPaymentInstallments->setData(array(
						'partial_payment_id'=>$partialPaymentId,
						'installment_amount'=>number_format($installmentAmount, 2, '.', ''),
						'installment_status'=>"Remaining",
						'installment_due_date'=>$date
					   ))->save();
						$partialPaymentInstallments->unsetData();
					}
				}
				
				# set Order Confirmatino Mail of Partial Payment
				if($order->getCustomerIsGuest()){
					$billingAddress = $order->getBillingAddress();
					$customerName = $billingAddress->getFirstName()." ".$billingAddress->getLastName();
				}else{
					$customerName = $order->getCustomerFirstName()." ".$order->getCustomerLastName();
				}	
				$this->emailSender->sendOrderSuccessEmail($customerName,$order->getCustomerEmail(),$order->getIncrementId(),$partialPaymentId,$order->getOrderCurrencyCode());
			}
			
			# unset partial payment session variable
			$this->calculationModel->unsetAllowPartialPayment();
	   	}
		catch(\Exception $e)
		{
			$this->logger->debug("exaption in save parial Payment order ");
			$this->logger->debug($e->getMessage());
			return $e->getMessage();
		}
    }    
}