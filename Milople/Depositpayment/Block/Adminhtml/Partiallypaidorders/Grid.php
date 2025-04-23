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
namespace Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
     /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \Milople\Grid\Model\GridFactory
     */
    protected $_gridFactory;
 
    /**
     * @var \Milople\Grid\Model\Status
     */
    protected $_status;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Milople\Grid\Model\GridFactory $gridFactory
     * @param \Milople\Grid\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Milople\Depositpayment\Model\PartialpaymentOrders $partiallypaidordersFactory,
        \Magento\Framework\Module\Manager $moduleManager,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\ResourceConnection $Resource,		
        array $data = []
    ) {
        $this->_gridFactory = $partiallypaidordersFactory;
        $this->moduleManager = $moduleManager;
		$this->_objectManager = $objectManager;
		$this->_resource= $Resource;		
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('grid_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$collection = $this->_gridFactory->getCollection();
		$collection->getSelect()->joinLeft(
					['order'=>$collection->getTable('sales_order')],
					'main_table.order_id = order.entity_id',
					['increment_id'=>'order.increment_id',
					 'firstname'=>'order.customer_firstname',
					 'lastname'=>'order.customer_lastname',
					 'email'=>'order.customer_email',
					 'order_date'=>'order.created_at',
					 'order_status'=>'order.status'
					])->joinLeft(
					['address'=>$collection->getTable('sales_order_address')],
					'main_table.order_id = address.parent_id AND address.address_type="billing"',
					['increment_id'=>'order.increment_id',
					 'guest_firstname'=>'address.firstname',
					 'guest_lastname'=>'address.lastname'					 
					]);
		$collection->setOrder('partial_payment_id','DESC');
		$this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
		$this->addColumn(
            'increment_id',
            [
                'header'	=> __('Order Id'),
                'index' 	=> 'increment_id',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'firstname',
            [
                'header' 	=> __('First Name'),
                'index' 	=> 'firstname',
                'class' 	=> 'xxx',
				'renderer'  => 'Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders\Edit\Tab\Renderer\FirstName'
            ]
        );
		$this->addColumn(
            'lastname',
            [
                'header' 	=> __('Last Name'),
                'index' 	=> 'lastname',
                'class' 	=> 'xxx',
				'renderer'  => 'Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders\Edit\Tab\Renderer\LastName'
            ]
        );
		$this->addColumn(
            'email',
            [
                'header' 	=> __('Email Address'),
                'index' 	=> 'email',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'paid_installments',
            [
                'header' 	=> __('Paid Installments'),
                'index' 	=> 'paid_installments',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'remaining_installments',
            [
                'header' 	=> __('Remaining Installments'),
                'index' 	=> 'remaining_installments',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'paid_amount',
            [
                'header' 	=> __('Paid amount'),
                'index' 	=> 'paid_amount',
				'type'		=> 'currency',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'remaining_amount',
            [
                'header' 	=> __('Remaining amount'),
                'index' 	=> 'remaining_amount',
				'type'		=> 'currency',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'order_date',
            [
                'header' 	=> __('Order Date'),
                'index' 	=> 'order_date',
				'type' 		=> 'date',
				'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'order_status',
            [
                'header' 	=> __('Status'),
                'index' 	=> 'order_status',
                'class' 	=> 'xxx',
				'type'		=> 'options',
				'options' 	=>  array(
							'pending' => 'Pending',
							'processing' => 'Processing',
							'canceled' => 'Canceled',
							'complete' => 'Complete'
							),
            ]
        ); 
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
 
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('partiallypaidorders');
        $this->getMassactionBlock()->setFormFieldName('partiallypaidorders');
 
		return $this;  
	  }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('depositpayment/*/index', ['_current' => true]);
    }
 
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }
}