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
namespace Milople\Depositpayment\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SalesOrderCreateProcessDataBefore implements ObserverInterface
{ 
	protected $logger;
	protected $messageManager;
	protected $serilize;
	const HASH_SEPARATOR = ":::";
	const DB_DELIMITER = "\r\n";
	
    public function __construct (
		\Magento\Framework\AuthorizationInterface $authorization,
		\Magento\Customer\Model\Session $customersession,
		\Magento\Framework\Serialize\SerializerInterface $serilize,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Backend\Model\Session\Quote $sessionQuote,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localedate,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Framework\App\Request\Http $request,
		\Milople\Depositpayment\Model\Calculation $calculationModel,
		\Milople\Depositpayment\Helper\Partialpayment $partialHelper,
		\Milople\Depositpayment\Helper\Data $dataHelper,
		\Psr\Log\LoggerInterface $logger
	 ) {
        $this->_authorization = $authorization;
		$this->serilize = $serilize;
		$this->customersession = $customersession; 
		$this->checkoutSession = $checkoutSession;
		$this->localeDate = $localedate; 
		$this->storedate = $date;
		$this->products = $productModel;
		$this->request = $request;
		$this->logger=$logger;
		$this->sessionQuote=$sessionQuote;
		$this->calculationModel = $calculationModel;
		$this->partialpaymentHelper = $partialHelper;
	}
 
    public function execute(Observer $observer)
    {
		
		$postData = $this->request->getPost();
		if (!isset($postData['item'])) {
			return;
		}
		if (isset($postData['update_items']) && $postData['update_items']) 
		{
			$quote = $this->calculationModel->getBackendQuote();
			$items = $postData['item'];
			if ($this->partialpaymentHelper->isWholeCart()) 
			{		
				if (isset($items[0]['allow_partial_payment'])) 
				{
					if (empty($items[0]['allow_partial_payment']) || $items[0]['allow_partial_payment'] == 0) 
					{
						
						$this->checkoutSession->setAllowPartialPayment(0);
						return;
					}
				}
				else 
				{
					$this->checkoutSession->setAllowPartialPayment(0);
					return;
				}
				$this->checkoutSession->setAllowPartialPayment($items[0]['allow_partial_payment']);
				
			}
			else 
			{	
				$child = array();
				foreach ($quote->getAllVisibleItems() as $id => $item) 
				{
					if (isset($items[$item->getId()]['allow_partial_payment']) ) 
					{
						$buyRequest = $item->getBuyRequest()->getData();
						$options = $item->getOptions();
						foreach ($options as $option) 
						{
							if($option->getCode() == 'info_buyRequest')
							{

								$unserialized = $this->serilize->unserialize($option->getValue());
								$unserialized['allow_partial_payment'] = $items[$item->getId()]['allow_partial_payment'];
								$option->setValue($this->serilize->serialize($unserialized))->save();
							}
						}
					    $child[]=$item->getItemId();
						$item->setOptions($options)->save();
					}
					
					$this->sessionQuote->setProductItem($child);
				}
			}
		}
		
    }    
}
