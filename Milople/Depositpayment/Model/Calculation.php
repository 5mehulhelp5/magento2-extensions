<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Partialpaymentpro
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/partial-payment-m2.html
*
**/
namespace Milople\Depositpayment\Model;

use Magento\Framework\Exception\NotFoundException;
use Magento\Catalog\Model\ProductFactory;
use Exception;
use \AllowDynamicProperties;

#[AllowDynamicProperties]
class Calculation
{
	protected $checkoutSession;

	public function __construct(
		\Magento\Catalog\Model\Product $product,
		ProductFactory $productFactory,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Backend\Model\Session\Quote $sessionQuote,
		\Magento\Framework\Pricing\Helper\Data $priceHelperData,
		\Magento\Payment\Model\Config $paymentModelConfig,
		\Magento\Payment\Helper\Data $paymentHelper,
		\Milople\Depositpayment\Helper\EmailSender $emailSender,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Milople\Depositpayment\Model\PartialpaymentInstallmentsFactory $installmentFactory,
		\Milople\Depositpayment\Model\PartialpaymentOrdersFactory $partialpaymentOrdersFactory,
		\Milople\Depositpayment\Model\PartialpaymentProductsFactory $partialpaymentProductsFactory,
		\Psr\Log\LoggerInterface $logger
	)
	{
		$this->partialHelper = $partialHelper;
		$this->checkoutSession = $checkoutSession;
		$this->product = $product;
		$this->storeDate = $date;
		$this->logger = $logger;
		$this->priceHelperData = $priceHelperData;
		$this->_paymentModelConfig = $paymentModelConfig;
		$this->paymentHelper = $paymentHelper;
		$this->sessionQuote = $sessionQuote;
		$this->installmentFactory = $installmentFactory->create();
		$this->partialpaymentOrdersFactory = $partialpaymentOrdersFactory->create();
		$this->orderFactory = $orderFactory->create();
		$this->partialpaymentProductsFactory = $partialpaymentProductsFactory->create();
		$this->emailSender = $emailSender;
		$this->_productFactory = $productFactory;
	}

	# get quote from checkout session
	public function getQuote()
	{
		if(!empty($this->checkoutSession->getQuote()->getAllVisibleItems())) {
			return $this->checkoutSession->getQuote();
		}else{
			return $this->sessionQuote->getQuote();
		}
	}
	public function getBackendQuote()
	{
		return $this->sessionQuote->getQuote();
	}

	# get down payment of perticular product or item
	public function getDownPaymentAmount($price,$product = NULL,$numberOfInstallments=NULL)
	{
		$calculationOn = $this->partialHelper->getCalculateDownpaymentOn($product);
		try
		{
			if($numberOfInstallments!=NULL)
			{
				return ($price / $numberOfInstallments);
			}
			$down_payment = $this->partialHelper->getDownPaymentValue($product);
			if($calculationOn=="2")// if type of calculation is percentage
			{
				$down_payment = ((int)$price*(int)$down_payment)/100;
			}
			return $down_payment;
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
	}

	# this function will check is valid data apply whole cart
	public function isAllowWholeCart($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		# if minimum amount set then check order have sufficient amount to apply wholecart
		if(!$this->partialHelper->isWholeCart() || ($this->partialHelper->getMinimumOrderAmount() > 0 && $this->partialHelper->getMinimumOrderAmount() > $quote->getBaseSubtotal()))
		{
			return false;//not allow wholecart as subtotal is less then minimum order amount
		}
		return true;
	}

	# calculate installment date as per input and return
	public function calculateInstallmentDates($date,$i)
	{
		$payment_plan = $this->partialHelper->getPaymentPlan();
		if($i>0)
		{
			if($payment_plan == 1) // for period month
			{
				$tempDate = date('Y-m-1', strtotime($date));
				$tempDateMonth = date('m',strtotime($tempDate . "+$i month"));
				$date = date('Y-m-d', strtotime($date . "+$i month"));
				if($tempDateMonth != date('m', strtotime($date)))
					$date = date('Y-m-t', strtotime($tempDate . "+$i month"));
			}
			else if($payment_plan == 2) // for period week
			{
				$date = date('Y-m-d', strtotime($date ."+$i week"));
			}
			else if($payment_plan == 3) // for period day(s)
			{
				$days = $this->partialHelper->getNumberOfDays() * ($i);
				$date = date('Y-m-d', strtotime($date ."+$days day"));
			}
		}
		$date = $this->partialHelper->formatDate($date);
		return $date;
	}

	# check that current product item have allow partial payment
	public function isAllowPartialPaymentInQuoteItem($item)
	{
		return $item->getBuyRequest()->getAllowPartialPayment();
	}
	# Get Number Of Installments For Flexy
	public function getNumberOfInstallmentsForFlexy(){
		if($this->checkoutSession->getAllowPartialPayment()){
			return $this->checkoutSession->getAllowPartialPayment();
		}
		return Null;
	}

	# check having partial payment applied product in cart
	public function havePartialPaymentProductInCart($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
		{
			return true;
		}
		else
		{
			$items = $quote->getAllVisibleItems();
			$groupIds=$this->checkoutSession->getGroupIds();
			foreach($items as $item)
			{
				$productId = $item->getProductId();//any product id
				$itemProduct = $this->_productFactory->create()->load($productId);
				if(($this->partialHelper->isAllowOnProducts($itemProduct->getApplyPartialPayment())) && (($this->isAllowPartialPaymentInQuoteItem($item) || (!empty($itemsIds) && in_array($item->getItemId(), $itemsIds)))|| (!empty($groupIds) && in_array($productId, $groupIds))))
					return true;
			}
		}
		return false;
	}

	# calculate total paying now amount
	public function getPayingNow($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$payingNow = 0;
		if($this->havePartialPaymentProductInCart($quote))
		{
			# if whole cart
			if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
			{
				if( $this->partialHelper->isAllowFlexyPayment())
				{
					$payingNow = $this->getDownPaymentAmount($quote->getSubtotal(),Null,$this->checkoutSession->getAllowPartialPayment());
				}
				else
				{
					$payingNow = $this->getDownPaymentAmount($quote->getSubtotal());
				}

			}
			else #if selected or all products
			{
				$items = $quote->getAllVisibleItems();
				$groupIds=$this->checkoutSession->getGroupIds();
				foreach($items as $item)
				{
					$productId = $item->getProductId();//any product id
					$itemProduct = $this->_productFactory->create()->load($productId);
					$itemsIds=$this->sessionQuote->getProductItem();
					$itemPrice = $item->getRowTotal()/$item->getQty();
					if(($this->partialHelper->isAllowOnProducts($itemProduct->getApplyPartialPayment())) && (($this->isAllowPartialPaymentInQuoteItem($item) || (!empty($itemsIds) && in_array($item->getItemId(), $itemsIds)))|| (!empty($groupIds) && in_array($productId, $groupIds))))
					{
						if( $this->partialHelper->isAllowFlexyPayment($itemProduct)){
							$numOfInstallment = null;
							if($this->isAllowPartialPaymentInQuoteItem($item)){
								$numOfInstallment = $this->isAllowPartialPaymentInQuoteItem($item);
							}elseif(!empty($groupIds) && in_array($productId, $groupIds)){
								try{
									$groupInstallmentNumber=$this->checkoutSession->getGroupInstallmentNumber();
									if(!empty($groupInstallmentNumber) && array_key_exists($productId,$groupInstallmentNumber)){
										$numOfInstallment = $groupInstallmentNumber[$productId];
									}else{
										$numOfInstallment = Null;
									}
								}catch(\Exception $e)
								{
									$numOfInstallment = 2;
								}
							}
							$payingNow += ($this->getDownPaymentAmount($itemPrice,$itemProduct,$numOfInstallment) * $item->getQty());
						}else{
							$payingNow += ($this->getDownPaymentAmount($itemPrice,$itemProduct) * $item->getQty());
						}

					}
					else
					{
						$payingNow += ($itemPrice * $item->getQty());
					}
				}
			}

			# set Tax, Shipping Amount, Surcharge and Discount as par setting
			if($payingNow>0)
			{
				# for Tax, Shipping and Surcharge amount
				if($this->partialHelper->isShippingAndTaxInPayingNow())
				{
					$payingNow += $quote->getShippingAddress()->getShippingAmount() + $quote->getTaxAmount();
				}
				# for Discount
				if($this->partialHelper->isDiscountInPayingNow())
				{
					$discount = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
					$payingNow -= $discount;
				}
				if($this->getInstallmentFeeAmount($quote)){
					$payingNow += $this->getInstallmentFeeAmount($quote);
				}
			}
		}
		return number_format($payingNow, 2, '.', '');
	}

	# calculate total base paying now amount
	public function getBasePayingNow($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$payingNow = 0;
		if($this->havePartialPaymentProductInCart($quote))
		{
			# if whole cart
			if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
			{
				if( $this->partialHelper->isAllowFlexyPayment())
				{
					$payingNow = $this->getDownPaymentAmount($quote->getBaseSubtotal(),Null,$this->checkoutSession->getAllowPartialPayment());
				}
				else
				{
					$payingNow = $this->getDownPaymentAmount($quote->getBaseSubtotal());
				}

			}
			else#if selected or all products
			{
				$items = $quote->getAllVisibleItems();
				$groupIds=$this->checkoutSession->getGroupIds();
				foreach($items as $item)
				{
					$productId = $item->getProductId();//any product id
					$itemProduct = $this->_productFactory->create()->load($productId);
					$itemsIds=$this->sessionQuote->getProductItem();
					$itemPrice = $item->getBaseRowTotal()/$item->getQty();
					if(($this->partialHelper->isAllowOnProducts($itemProduct->getApplyPartialPayment())) && (($this->isAllowPartialPaymentInQuoteItem($item) || (!empty($itemsIds) && in_array($item->getItemId(), $itemsIds)))|| (!empty($groupIds) && in_array($productId, $groupIds))))
					{
						if( $this->partialHelper->isAllowFlexyPayment($itemProduct)){
							$numOfInstallment = null;
							if($this->isAllowPartialPaymentInQuoteItem($item)){
								$numOfInstallment = $this->isAllowPartialPaymentInQuoteItem($item);
							}elseif(!empty($groupIds) && in_array($productId, $groupIds)){
								try{
									$groupInstallmentNumber=$this->checkoutSession->getGroupInstallmentNumber();
									if(!empty($groupInstallmentNumber) && array_key_exists($productId,$groupInstallmentNumber)){
										$numOfInstallment = $groupInstallmentNumber[$productId];
									}else{
										$numOfInstallment = Null;
									}
								}catch(\Exception $e)
								{
									$numOfInstallment = 2;
								}
							}
							$payingNow += ($this->getDownPaymentAmount($itemPrice,$itemProduct,$numOfInstallment) * $item->getQty());
						}else{
							$payingNow += ($this->getDownPaymentAmount($itemPrice,$itemProduct) * $item->getQty());
						}

					}
					else
					{
						$payingNow += ($itemPrice * $item->getQty());
					}
				}
			}

			# set Tax, Shipping Amount, Surcharge and Discount as par setting
			if($payingNow>0)
			{
				# for Tax, Shipping and Surcharge amount
				if($this->partialHelper->isShippingAndTaxInPayingNow())
				{
					$payingNow += $quote->getShippingAddress()->getBaseShippingAmount() + $quote->getBaseTaxAmount();
				}
				# for Discount
				if($this->partialHelper->isDiscountInPayingNow())
				{
					$discount = $quote->getBaseSubtotal() - $quote->getBaseSubtotalWithDiscount();
					$payingNow -= $discount;
				}
				if($this->getBaseInstallmentFeeAmount($quote)){
					$payingNow += $this->getBaseInstallmentFeeAmount($quote);
				}
			}
		}
		return number_format($payingNow, 2, '.', '');
	}

	# Return amount to be paid later
	public function getAmountToBePaidLater($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$amountPaidLater = 0;
		if($this->getPayingNow($quote)!=0 && $this->havePartialPaymentProductInCart($quote))
		{
			$amountPaidLater = $quote->getGrandTotal() - number_format($this->getPayingNow($quote), 2, '.', '');
		}
		return $amountPaidLater;
	}
	# Return amount to be paid later
	public function getBaseAmountToBePaidLater($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$amountPaidLater = 0;
		if($this->getPayingNow($quote)!=0 && $this->havePartialPaymentProductInCart($quote))
		{
			$amountPaidLater = $quote->getBaseGrandTotal() - number_format($this->getBasePayingNow($quote), 2, '.', '');
		}
		return $amountPaidLater;
	}

	public function getBaseTotalInstallmentFee($quote=null){
		if(!$quote){
			$quote = $this->getQuote();
		}
		$baseInstallmentFee=0;
		if($this->partialHelper->installmentFeeStatus() == 1){
			$baseInstallmentFee = $this->getBaseInstallmentFeeAmount($quote);
		}
		elseif($this->partialHelper->installmentFeeStatus() == 2){
			if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
			{
				$baseInstallmentFee = $this->getBaseInstallmentFeeAmount($quote) * 2;
			}
			else{
				$total_installments = 0;
				$items = $quote->getAllVisibleItems();
				if($this->isAllowWholeCart($quote)){
					if($this->partialHelper->isAllowFlexyPayment()){
						$total_installments = $this->getNumberOfInstallmentsForFlexy();
					}else{
						$total_installments = $this->partialHelper->getNumberOfInstallments();
					}
				}else{// All / Specific Products
					foreach($items as $item)
					{
						$product = $this->product->load($item->getProductId());
						$price = $item->getBaseRowTotal();
						if($this->isAllowPartialPaymentInQuoteItem($item)){//
							if($this->partialHelper->isAllowFlexyPayment($product) && $this->isAllowPartialPaymentInQuoteItem($item) > $total_installments)
							{
								$total_installments = $this->isAllowPartialPaymentInQuoteItem($item);
							}
							else if(!$this->partialHelper->isAllowFlexyPayment($product) && $this->partialHelper->getTotalIinstallments($product) > $total_installments)
							{
								$total_installments = $this->partialHelper->getTotalIinstallments($product);
							}
						}
					}
				}
				$baseInstallmentFee = $this->getBaseInstallmentFeeAmount($quote) * $total_installments;
			}
		}
		return $baseInstallmentFee;
	}
	// stripe
	public function getDownPaymentForOrder($order){
		$installments = $this->installmentFactory->getInstallmentsByOrderId($order->getEntityId());
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->log(100,print_r("Total installment".$installments->count(),true));
		if($installments->count()){
			$amount = $installments->getFirstItem()->getInstallmentAmount();
		}else{
			$amount = $order->getPaidAmount();
		}
		return $amount;
	}
	// stripe
	public function getTotalInstallmentFee($quote=null){
		if(!$quote){
			$quote = $this->getQuote();
		}
		$installmentFee=0;
		if($this->partialHelper->installmentFeeStatus() == 1){
			$installmentFee = $this->getInstallmentFeeAmount($quote);
		}
		elseif($this->partialHelper->installmentFeeStatus() == 2){
			$total_installments = 0;
			$items = $quote->getAllVisibleItems();
			if($this->isAllowWholeCart($quote)){
				if($this->partialHelper->isAllowFlexyPayment()){
					$total_installments = $this->getNumberOfInstallmentsForFlexy();
				}else{
					$total_installments = $this->partialHelper->getNumberOfInstallments();
				}
			}else{// All / Specific Products
				foreach($items as $item)
				{
					$product = $this->product->load($item->getProductId());
					$price = $item->getBaseRowTotal();
					if($this->isAllowPartialPaymentInQuoteItem($item)){//
						if($this->partialHelper->isAllowFlexyPayment($product) && $this->isAllowPartialPaymentInQuoteItem($item) > $total_installments)
						{
							$total_installments = $this->isAllowPartialPaymentInQuoteItem($item);
						}
						else if(!$this->partialHelper->isAllowFlexyPayment($product) && $this->partialHelper->getTotalIinstallments($product) > $total_installments)
						{
							$total_installments = $this->partialHelper->getTotalIinstallments($product);
						}
					}
				}
			}
			$installmentFee = $this->getInstallmentFeeAmount($quote) * $total_installments;
		}
		return $installmentFee;
	}
	# Return amount of Base Installment Fee Amount
	public function getBaseInstallmentFeeAmount($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$baseInstallmentFee=0;
		if($this->partialHelper->installmentFeeStatus()){
			$items = $quote->getAllVisibleItems();
			$groupIds=$this->checkoutSession->getGroupIds();
			$itemsIds=$this->sessionQuote->getProductItem();
			foreach($items as $item)
			{
				$price = $item->getBaseRowTotal()/$item->getQty();
				$productId = $item->getProductId();//any product id
				$itemProduct = $this->_productFactory->create()->load($productId);
				if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
				{
					$baseInstallmentFee = ($baseInstallmentFee + $this->getInstallmentFeeByPrice($price))* $item->getQty();
				}
				elseif(($this->partialHelper->isAllowOnProducts($itemProduct->getApplyPartialPayment())) && (($this->isAllowPartialPaymentInQuoteItem($item) || (!empty($itemsIds) && in_array($item->getItemId(), $itemsIds)))|| (!empty($groupIds) && in_array($productId, $groupIds))))
				{
					$baseInstallmentFee = ($baseInstallmentFee + $this->getInstallmentFeeByPrice($price))* $item->getQty();
				}
			}
		}
		return $baseInstallmentFee;
	}
	# Return amount of Installment Fee Amount
	public function getInstallmentFeeAmount($quote=null)
	{
		if(!$quote){
			$quote = $this->getQuote();
		}
		$installmentFee=0;
		if($this->partialHelper->installmentFeeStatus()){
			$items = $quote->getAllVisibleItems();
			$groupIds=$this->checkoutSession->getGroupIds();
			$itemsIds=$this->sessionQuote->getProductItem();
			foreach($items as $item)
			{
				$price = $item->getRowTotal()/$item->getQty();
				$productId = $item->getProductId();//any product id
				$itemProduct = $this->_productFactory->create()->load($productId);
				if($this->isAllowWholeCart($quote) && ($this->checkoutSession->getAllowPartialPayment() || $this->sessionQuote->getAllowPartialPayment() || !$this->partialHelper->isAllowFullPayment()))
				{
					$installmentFee = ($installmentFee + $this->getInstallmentFeeByPrice($price))* $item->getQty();
				}
				elseif(($this->partialHelper->isAllowOnProducts($itemProduct->getApplyPartialPayment())) && (($this->isAllowPartialPaymentInQuoteItem($item) || (!empty($itemsIds) && in_array($item->getItemId(), $itemsIds)))|| (!empty($groupIds) && in_array($productId, $groupIds))))
				{
					$installmentFee = ($installmentFee + $this->getInstallmentFeeByPrice($price))* $item->getQty();
				}
			}
		}
		return $installmentFee;
	}
	public function getInstallmentFeeByPrice($amount)
	{
		$installmentFee = 0;
		if($this->partialHelper->installmentFeeValue()){
			if($this->partialHelper->installmentFeeType() == 1){
				$installmentFee = $this->partialHelper->installmentFeeValue();
			}elseif($this->partialHelper->installmentFeeType() == 2){
				$installmentFee = ($amount * $this->partialHelper->installmentFeeValue())/100;
			}
		}
		return $installmentFee;
	}
	public function getInstallmentLabel()
	{
		$label = "Installment Fee";
		if($this->partialHelper->installmentFeeLabel()){
			$label = $this->partialHelper->installmentFeeLabel();
		}
		return $label;
	}
	public function setInstallmentSuccessData($installmentId,$paymentMethod,$freeAmount=0)
	{
		# update installment table
		$installmentData = $this->installmentFactory->load($installmentId);
		$installmentData->setInstallmentStatus('Paid')
						->setInstallmentPaidDate($this->calculateInstallmentDates($this->storeDate->date('m/d/Y'),1))
						->setPaymentMethod($paymentMethod)
						->save();
		$this->setInstallmentData($installmentId,true);
	}
	public function setInstallmentUnPaid($installmentId)
	{
		# update installment table
		$installmentData = $this->installmentFactory->load($installmentId);
		$installmentData->setInstallmentStatus('Remaining')->setInstallmentPaidDate("")->setPaymentMethod("")->save();

		$this->setInstallmentData($installmentId,false);
	}
	private function setInstallmentData($installmentId,$paid)
	{
		# calculate amounts
		$installmentData = $this->installmentFactory->load($installmentId);
		$installments = $this->installmentFactory->getCollection()->addFieldToFilter('partial_payment_id',$installmentData->getPartialPaymentId());
		$partialpaymentData = $this->partialpaymentOrdersFactory->load($installmentData->getPartialPaymentId());
		$basePaidAmount = 0;
		$paidInstallments = 0;

		foreach($installments as $installment)
		{
			if($installment->getInstallmentStatus() == 'Paid')
			{
				$basePaidAmount += $installment->getInstallmentAmount();
				$paidInstallments++;
			}
		}
		$baseRemainingAmount = $partialpaymentData->getTotalAmount() - $basePaidAmount;
		$remainingInstallments = $partialpaymentData->getTotalInstallments() - $paidInstallments;
		#update partial payment order table
		$partialpaymentData->setPaidInstallments($paidInstallments)
						->setRemainingInstallments($remainingInstallments)
						->setPaidAmount($basePaidAmount)
						->setRemainingAmount($baseRemainingAmount)
						->save();

		#update order table
		$order = $this->orderFactory->load($partialpaymentData->getOrderId());
		$order->setPaidAmount($basePaidAmount)
			->setBasePaidAmount($basePaidAmount)
			->setRemainingAmount($baseRemainingAmount)
			->setBaseRemainingAmount($baseRemainingAmount)
			->save();

		#update partial payment product table
		$partialpaymentProductData = $this->partialpaymentProductsFactory->getCollection()->addFieldToFilter('partial_payment_id',$partialpaymentData->getId());

		foreach($partialpaymentProductData as $data)
		{
			if($paid)
			{
				if($partialpaymentData->getRemainingAmount()==0)//if all installments are paid set all values properlly
				{
					$data->setPaidInstallments($data->getTotalInstallments())
						->setPaidAmount($data->getTotalAmount())
						->setRemainingInstallments(0)
						->setRemainingAmount(0)
						->save();
				}
				else
				{
					$data->setPaidInstallments($data->getPaidInstallments()+1);
					$paidAmount = $data->getPaidAmount() + $installmentData->getInstallmentAmount();
					$data->setRemainingInstallments($data->getRemainingInstallments()-1)
						->setPaidAmount($paidAmount)
						->setRemainingAmount($data->getTotalAmount()-$paidAmount)
						->save();
				}
			}
			else
			{
				if($partialpaymentData->getPaidAmount()==0)//if all installments are paid set all values properlly
				{
					$data->setPaidInstallments(0)
						->setPaidAmount(0)
						->setRemainingInstallments($data->getTotalInstallments())
						->setRemainingAmount($data->getTotalAmount())
						->save();
				}
				else
				{
					$data->setPaidInstallments($data->getPaidInstallments()-1);
					$paidAmount = $data->getPaidAmount() - $installmentData->getInstallmentAmount();
					$data->setRemainingInstallments($data->getRemainingInstallments()+1)
						->setPaidAmount($paidAmount)
						->setRemainingAmount($data->getTotalAmount()-$paidAmount)
						->save();
				}
			}
		}
		if($order->getCustomerIsGuest()){//fetch customer name
			$billingAddress = $order->getBillingAddress();
			$customerName = $billingAddress->getFirstName()." ".$billingAddress->getLastName();
		}else{
			$customerName = $order->getCustomerFirstName()." ".$order->getCustomerLastName();
		}
		if($paid)
		{
			#send installment success mail
			$this->emailSender->sendInstallmentPaymentConfirmationMail($customerName,$order->getCustomerEmail(),$order->getIncrementId(), $installmentData->getPartialPaymentId(), $installmentData->getInstallmentAmount(),$order->getOrderCurrencyCode());
		}
		else
		{
			#send installment failer mail
			if($installmentData->getInstallmentStatus() === "Failed" ){
				$this->emailSender->sendInstallmentPaymentFailureMail($customerName,$order->getCustomerEmail(),$order->getIncrementId(), $installment->getPartialPaymentId(), $installmentData->getInstallmentAmount(), $order->getOrderCurrencyCode());
			}
		}
	}

	# unset session variable 'allow partial payment' of wholecart
	public function unsetAllowPartialPayment()
	{
			$this->checkoutSession->unsAllowPartialPayment();
	}
	public function checkCreditLimit($customerId,$totalRemainingAmount,$currencyCode)
	{
		if($totalRemainingAmount <= 0 || $this->partialHelper->getMaximumCreditLimit() == 0 )//if remaining amount is not set(not partial payment order)
		{
			return false;
		}
		$limit_amount = $this->priceHelperData->currency($this->partialHelper->getMaximumCreditLimit(),$currencyCode,false);
		$message = str_replace("{credit_limit}",$limit_amount,$this->partialHelper->getCreditLimitSurpassMessage());
		$orders = $this->orderFactory->getCollection()->addFieldToFilter('customer_id',$customerId);

		foreach($orders as $order)
		{
			$totalRemainingAmount += $order->getBaseRemainingAmount();
		}
		// if credit limit exceed then return true;
		if($totalRemainingAmount > $this->partialHelper->getMaximumCreditLimit())
		{
			return $message;
		}
		return false;
	}
	public function getActiveOfflinePaymentMethodes(){
		$payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
			$paymentMethod = $this->paymentHelper->getMethodInstance($paymentCode);
			if($paymentMethod->isOffline()){
				$path = 'payment/'.$paymentCode.'/title';
				$paymentTitle = $this->partialHelper->getValueByPath($path);
				$methods[$paymentCode] = $paymentTitle;
			}
        }
		return $methods;
	}

	public function getPaymentTitle($code)
	{
		if($code!='')
		{
			$activeMethods = $this->getActiveOfflinePaymentMethodes();
			if(isset($activeMethods[$code]))
				return $this->paymentHelper->getMethodInstance($code)->getTitle();
			else
				return $code;
		}
		else
			return $code;
	}
}
