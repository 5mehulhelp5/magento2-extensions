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
namespace Milople\Depositpayment\Block\Adminhtml\Reports\Installments;
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
        \Milople\Depositpayment\Model\PartialpaymentInstallments $partialpaymentInstallmentsFactory,
        \Milople\Depositpayment\Model\Calculation $calculationModel,
        \Magento\Framework\Module\Manager $moduleManager,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\App\ResourceConnection $Resource,		
        array $data = []
    ) {
        $this->_gridFactory  = $partialpaymentInstallmentsFactory;
		$this->calculationModel = $calculationModel;
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
        $this->setId('increment_id');
        $this->setDefaultSort('increment_id');
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
		$collection->getSelect()
			->joinLeft(
			['partial_order'=>$collection->getTable('partial_payment_orders')],
			'main_table.partial_payment_id = partial_order.partial_payment_id',
			[
			])->joinLeft(
			['order'=>$collection->getTable('sales_order')],
			'partial_order.order_id = order.entity_id',
			['increment_id'=>'order.increment_id',
			 'firstname'=>'order.customer_firstname',
			 'lastname'=>'order.customer_lastname',
			 'email'=>'order.customer_email'			 
			])->joinLeft(
			['address'=>$collection->getTable('sales_order_address')],
			'partial_order.order_id = address.parent_id AND address.address_type="billing"',
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
            'installment_amount',
            [
                'header' 	=> __('Installment Amount'),
                'index' 	=> 'installment_amount',
				'type' => 'currency',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'installment_due_date',
            [
                'header' 	=> __('Installment Due Date'),
                'index' 	=> 'installment_due_date',
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'installment_paid_date',
            [
                'header' 	=> __('Installment Paid Date'),
                'index' 	=> 'installment_paid_date',				
                'class' 	=> 'xxx'
            ]
        );
		$this->addColumn(
            'installment_status',
            [
                'header' 	=> __('Installment Status'),
                'index' 	=> 'installment_status',
				'type'		=> 'options',
				'options' =>  array(
					'Paid' => 'Paid',
					'Remaining' => 'Remaining',
					'Canceled' => 'Canceled',
					'Failed' => 'Failed',
				),	
                'class' 	=> 'xxx'
            ]
        );	
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
		$this->addExportType('*/*/exportInstallmentsCsv', __('CSV'));
		$this->addExportType('*/*/exportInstallmentsXml', __('XML'));
        return parent::_prepareColumns();
    }
 
    protected function _prepareMassaction()
    {
       $this->setMassactionIdField('partial_payment_id');
		$this->getMassactionBlock()->setFormFieldName('partialpaymentInstallments');
		$methods = $this->calculationModel->getActiveOfflinePaymentMethodes();
		$this->getMassactionBlock()->addItem('Remind', array(
			'label' =>__('Send Installment Reminder Email'),
			'url' => $this->getUrl('*/*/massInstallmentsRemind'),
		));
		$this->getMassactionBlock()->addItem('changestatus', array(
			'label' => __('Send Installment Overdue Email'),
			'url' => $this->getUrl('*/*/massInstallmentsOverdue'),
		));
		if($methods){
			$this->getMassactionBlock()->addItem('pay', array(
				'label' =>__('Make Payment for selected Installments'),
				'url' => $this->getUrl('*/*/MassInstallmentsPay', array('_current' => true)),
				'additional' => array(
					'visibility' => array(
						'name' => 'pay',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => __('Select Payment Method'),
						'options' =>  $methods
					)
				)				
			));
		}	
		return $this;  
	  }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('depositpayment/*/installments', ['_current' => true]);
    }    
}