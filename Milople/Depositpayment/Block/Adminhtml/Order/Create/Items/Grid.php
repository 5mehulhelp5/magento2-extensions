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
namespace Milople\Depositpayment\Block\Adminhtml\Order\Create\Items;

/**
 * Adminhtml sales order create items grid block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * This is overriden to display partial payment setting Admin Order create
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    public function __construct(
       	\Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GiftMessage\Helper\Message $messageHelper,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
		\Milople\Depositpayment\Helper\Data $data_helper,
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialpayment_helper,
        array $data = []
    ) {
		$this->helper = $data_helper;
		$this->partialpayment_helper = $partialpayment_helper;
		$this->productModel = $productModel;
		$this->calculationModel = $calculationModel;
		$this->checkoutSession = $checkoutSession;
		$this->customerFactory = $customerFactory;
		$this->objectManager = $objectManager;
		$this->quoteItems = $this->calculationModel->getQuote()->getAllItems();
		
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency,$wishlistFactory, $giftMessageSave, $taxConfig, $taxData, $messageHelper, $stockRegistry, $stockState, $data);
		
		#partial payment settings
		$this->isValid = $this->helper->canRun();
		$this->status = $this->partialpayment_helper->getStatus();
    }
	
	# to check that show partial payment options when selected or all product set
	public function canShowPrtialPaymentOnProduct()
	{
		$customerGroupId = $this->customerFactory->create()->load($this->getCustomerId())->getGroupId();
		$isValidCustomer = $this->partialpayment_helper->isValidCustomer($customerGroupId);
		if ($this->status && $this->isValid && $this->partialpayment_helper->isAllProducts() && $isValidCustomer || $this->partialpayment_helper->isSelectedProducts() && $isValidCustomer)
		{
			return true;
		}
	}
	
	# to check that show partial payment options when whole cart
	public function canShowPrtialPaymentOnWholeCart()
	{
		$customerGroupId = $this->customerFactory->create()->load($this->getCustomerId())->getGroupId();
		$isValidCustomer = $this->partialpayment_helper->isValidCustomer($customerGroupId);
		if($this->status && $this->isValid && $this->partialpayment_helper->isWholeCart() && $isValidCustomer) 
		{
			return true;
		}
	}
	
	# display partial payment option in admin order create item grid
	public function getPartialpaymentData($isProduct,$item=NULL)
	{
		$allowPartialPayment = NULL;
		$partialpaymentLabel = $this->partialpayment_helper->getPartialpaymentLabel();
		$show = FALSE; // to check display partial payment option or not
		
		if($isProduct)//All Product or Selected product
		{
			$product = $this->productModel->load($item->getProductId());
			$isAllowFullPayment = $this->partialpayment_helper->isAllowFullPayment($product->getAllowFullPayment());
			$isAllowOnThisProduct = $this->partialpayment_helper->isAllowOnProducts($product->getApplyPartialPayment());
			$isFlexy = $this->partialpayment_helper->isAllowFlexyPayment($product);
			$totalInstallments = $this->partialpayment_helper->getTotalIinstallments($product);
			if($isAllowOnThisProduct)
			{
				$show = TRUE;
				$itemId = $item->getId();
				$allowPartialPayment = $this->calculationModel->isAllowPartialPaymentInQuoteItem($item);
			}
		}
		else
		{
			$itemId = 0;
			$isAllowOnThisProduct = TRUE;
			$isAllowFullPayment = $this->partialpayment_helper->isAllowFullPayment();
			$show = TRUE;
			$allowPartialPayment = $this->checkoutSession->getAllowPartialPayment();
			$isFlexy = $this->partialpayment_helper->isAllowFlexyPayment();
			$totalInstallments = $this->partialpayment_helper->getTotalIinstallments();
		}
		
		if($show == TRUE)
		{
			$html='';
			if(!$isProduct)
			{
				$html=$html. __('This product is available with').'<strong> '.$partialpaymentLabel.'</strong> :- ';
			}
			if($isAllowFullPayment)
			{
				$html=$html. '<select class="partial-payment admin__control-select" name=item['.$itemId.'][allow_partial_payment]>
					<option value="0">'.__('Full Payment').'</option>';
				if($isFlexy){
					for ($i=2;$i<=$totalInstallments;$i++) {
						if($allowPartialPayment==$i)
						{
							$html=$html. '<option value="'.$i.'" selected="selected">'.$i.' '.__('Installment').'</option>';
						}
						else
						{
							//$html=$html. '<option value="1">'.__('Installment').'</option>';
							$html=$html. '<option value="'.$i.'">'.$i.' '.__('Installments').'</option>';
						}
					}
				}else{	
					if($allowPartialPayment==1)
					{
						$html=$html. '<option value="1" selected="selected">'.__('Installments').'</option>';
					}
					else
					{
						$html=$html. '<option value="1">'.__('Installment').'</option>';
					}
				}
				$html=$html. '</select>';
			}
			else
			{
				if($isProduct)
				{
					$html=$html. __('This product is available with').'<strong> '.$partialpaymentLabel.'</strong>';
				}
				$html=$html. '<input type="hidden" class="partial-payment admin__control-select" name=item['.$itemId.'][allow_partial_payment] value="1">';
			}
		}

		return $html;
	}
}
