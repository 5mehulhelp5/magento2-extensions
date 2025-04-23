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
namespace Milople\Depositpayment\Model\Config\Backend;

class DownPayment extends \Magento\Framework\App\Config\Value
{
     protected $directoryHelper;


    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(		
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,	
        \Milople\Depositpayment\Helper\Data $data_helper,	
        array $data = []		
    ) {		
        $this->directoryHelper = $directoryHelper;
		$this->helper = $data_helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
    }   
    public function beforeSave()
    {
		$groupData = $this->_getData('fieldset_data');
    	$value = $this->getValue();
		if($groupData['partialpayment_type'] == 0){
			if($groupData['calculate_downpayment_on'] == 2 && ($value < 1 || $value >= 100)){
				throw new \Magento\Framework\Exception\LocalizedException(__("Down Payment must be between 1 to 100 when 'Calculate Down Payment On' is set as Percentage"));
			}
		}		
    }
}
