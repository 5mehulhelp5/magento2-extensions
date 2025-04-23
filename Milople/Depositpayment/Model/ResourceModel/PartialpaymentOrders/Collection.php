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
namespace Milople\Depositpayment\Model\ResourceModel\PartialpaymentOrders;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'partial_payment_id';
    const YOUR_TABLE = 'partial_payment_orders';
	
     public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Milople\Depositpayment\Model\PartialpaymentOrders',
            'Milople\Depositpayment\Model\ResourceModel\PartialpaymentOrders'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }
	/**
	 * Define resource model
	 */
	/* protected function _construct()
	{
		$this->_init('Milople\Depositpayment\Model\PartialpaymentOrders','Milople\Depositpayment\Model\ResourceModel\PartialpaymentOrders');
	} */
	protected function _initSelect()
    {
        parent::_initSelect();
 
        /* $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('wk_record_temp')], //2nd table name by which you want to join mail table
            'main_table.record_id = secondTable.record_id', // common column which available in both table 
            '*' // '*' define that you want all column of 2nd table. if you want some particular column then you can define as ['column1','column2']
        ); */
        $this->getSelect()->joinLeft(
            ['order'=>$this->getTable('sales_order')],
            'main_table.order_id = order.entity_id',
            ['increment_id'=>'order.increment_id',
             'firstname'=>'order.customer_firstname',
             'lastname'=>'order.customer_lastname',
             'email'=>'order.customer_email',
             'order_date'=>'order.created_at',
             'order_status'=>'order.status'
            ])->joinLeft(
            ['address'=>$this->getTable('sales_order_address')],
            'main_table.order_id = address.parent_id AND address.address_type="billing"',
            ['increment_id'=>'order.increment_id',
             'guest_firstname'=>'address.firstname',
             'guest_lastname'=>'address.lastname'					 
            ]);
    }
	public function addCustomerFilter($customer)
    {
        if (!is_int($customer)) {
            $id = $customer->getId();
        } else {
            $id = $customer;
        }
        $this->getSelect()->where('customer_id=?', $id);
        return $this;
    }
}
