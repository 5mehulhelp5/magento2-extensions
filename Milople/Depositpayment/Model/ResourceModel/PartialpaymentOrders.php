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
namespace Milople\Depositpayment\Model\ResourceModel;

class PartialpaymentOrders extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
     * @var string
     */
    protected $_idFieldName = 'partial_payment_id';
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
	/**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) 
    {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }
	/**
	 * Define resource model
	 */
	protected function _construct()
	{
		$this->_init('partial_payment_orders','partial_payment_id');
	}
}
