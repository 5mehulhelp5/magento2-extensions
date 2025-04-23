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
use Magento\Framework\Exception\NotFoundException;
use Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class SalesOrderPlaceBefore implements ObserverInterface
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
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Milople\Depositpayment\Helper\Data $dataHelper
	   ) {
        $this->_authorization = $authorization;
		$this->customersession = $customersession; 
		$this->localeDate = $localedate; 
		$this->storedate = $date;
		$this->products = $productModel;
		$this->calculationModel = $calculationModel;
		$this->partialHelper = $partialHelper;
		$this->messageManager = $messageManager;
    }
 
    public function execute(Observer $observer)
    {
		$order = $observer->getOrder();
		$PaidAmount = $this->calculationModel->getPayingNow();
		$basePaidAmount = $this->calculationModel->getBasePayingNow();
		$RemainingAmount = $this->calculationModel->getAmountToBePaidLater();		
		$baseRemainingAmount = $this->calculationModel->getBaseAmountToBePaidLater();
		$Installmentfee = $this->calculationModel->getTotalInstallmentFee();
        $baseInstallmentfee = $this->calculationModel->getBaseTotalInstallmentFee();	
		try
		{
			$message = $this->calculationModel->checkCreditLimit($order->getCustomerId(),$baseRemainingAmount,$order->getOrderCurrencyCode());
			if($message)
			{
				throw new LocalizedException(__($message));
			}
		}
		catch(\Magento\Framework\Exception\LocalizedException $e)
		{
			$error = true;
            throw new CouldNotSaveException(
                __($message),
                $e
            );
		}catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($message),
                $e
            );
        }    
		try
		{
			$order->setPaidAmount($PaidAmount);
			$order->setBasePaidAmount($basePaidAmount);
			$order->setRemainingAmount($RemainingAmount);
			$order->setBaseRemainingAmount($baseRemainingAmount);
			$order->setInstallmentFee($Installmentfee);
			$order->setBaseInstallmentFee($baseInstallmentfee);
		}
		catch(\Exception $e)
		{
			return $e->getMessage();
		}
    }    
}