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
class UpdateCart implements ObserverInterface
{
	protected $messageManager;
	protected $serilize;
	
	public function __construct(
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Serialize\SerializerInterface $serilize,
		\Magento\Directory\Helper\Data $data,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Product $product
		) {
        $this->request = $request;
		$this->serilize = $serilize;
 		$this->messageManager = $messageManager;
		$this->currencyConvertHelper = $data;
		$this->_storeManager = $storeManager;
		$this->_product = $product;
	 }
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$postdata = $this->request->getPost();
		if(isset($postdata['allow_partial_payment']))
		{
			$item = $observer->getEvent()->getItem();
			$buyInfo = $item->getProduct()->getCustomOption('info_buyRequest');
			$buyRequestArr = $this->serilize->unserialize($buyInfo->getValue());
			$buyRequestArr['allow_partial_payment'] = $postdata['allow_partial_payment'];
			$buyInfo->setValue($this->serilize->serialize($buyRequestArr))->save();
		}
	}
}