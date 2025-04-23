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
namespace Milople\Depositpayment\Block\Adminhtml\Reports\Revenue;

/**
 * Adminhtml sales report grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	/**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory
     * @param \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory
     * @param \Magento\Reports\Helper\Data $reportsData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,        		
        \Magento\Reports\Helper\Data $reportsData,
		\Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
		\Magento\Sales\Model\Order $orderModel,
        \Milople\Depositpayment\Controller\Adminhtml\Reports\Revenue $revenue,
        array $data = []
    ) {
		$this->_collectionFactory = $collectionFactory;
        $this->orderModel = $orderModel;
        $this->revenue = $revenue;
        parent::__construct($context, $backendHelper, $data);
    }
	protected $_countTotals = true;
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
		$this->setFilterVisibility(false);
        //$this->setPagerVisibility(false);
        $this->setUseAjax(false);
        $this->revenue->_initReportAction($this);
	//	\Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Reports\Controller\Adminhtml\Report\AbstractReport::class)->_initReportAction($this);
    }
	 /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        if ($this->_collection === null) {
            $this->setCollection($this->_collectionFactory->create());
        }
        return $this->_collection;
    }
    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        //return 'Magento\Sales\Model\ResourceModel\Report\Order\Collection';
        return 'Milople\Depositpayment\Model\ResourceModel\Reports\Revenue\Collection';
    }
	protected function _prepareCollection()
    {
		$filterData = $this->getFilterData();
		if ($filterData->getData('from') == null || $filterData->getData('to') == null) {
            $this->setCountTotals(false);
            $this->setCountSubTotals(false);
            return parent::_prepareCollection();
        }
		$collection2 = $this->orderModel->getCollection()->addFieldToSelect('created_at');		
		$collection2->getSelect()->
		joinLeft('partial_payment_orders', 'main_table.entity_id = partial_payment_orders.order_id',array());
		$collection2->getSelect()                
                ->columns([
					'total_no_of_orders_with_partial_payment'=> new \Zend_Db_Expr('count(order_id)'),					
					'total_amount_of_partial_payment_orders'=> new \Zend_Db_Expr('sum(case when order_id = entity_id then ((IFNULL(base_total_invoiced, 0) - IFNULL(base_tax_invoiced, 0) - IFNULL(base_shipping_invoiced, 0) - (IFNULL(base_total_refunded, 0) - IFNULL(base_tax_refunded, 0) - IFNULL(base_shipping_refunded, 0))) * IFNULL(base_to_global_rate, 0)) else 0 end)'),
					'total_no_of_orders_without_partial_payment'=> new \Zend_Db_Expr('count(entity_id)-count(order_id)'),
					'total_amount_of_non_partial_payment_orders'=> new \Zend_Db_Expr('sum(case when order_id then 0 else  ((IFNULL(base_total_invoiced, 0) - IFNULL(base_tax_invoiced, 0) - IFNULL(base_shipping_invoiced, 0) - (IFNULL(base_total_refunded, 0) - IFNULL(base_tax_refunded, 0) - IFNULL(base_shipping_refunded, 0))) * IFNULL(base_to_global_rate, 0)) end)')
					])
                ->group('date(created_at)');
		$collection2->addFieldToFilter('created_at', ['lteq' => $filterData->getData('to', null)]);
        $collection2->addFieldToFilter('created_at', ['gteq' => $filterData->getData('from', null)]);
       
		$this->setCollection($collection2);
        //parent::_prepareCollection();		
        return $this;
	}
	public function getTotals()
    {
        $totals = new \Magento\Framework\DataObject();
        $fields = array(
            'total_no_of_orders_with_partial_payment' => 0,
            'total_amount_of_partial_payment_orders' => 0,
            'total_no_of_orders_without_partial_payment' => 0,
            'total_amount_of_non_partial_payment_orders' => 0,

        );
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }
        $totals->setData($fields);
        return $totals;
    }	
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
		/* if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        } */
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);		
        $this->addColumn(
            'period',
            [
                'header' => __('Interval'),
                'index' => 'created_at',
                'sortable' => false,
				'filter'  => false,
                'period_type' => $this->getPeriodType(),
                'renderer' => 'Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date',
                'totals_label' => __('Total'),				
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );
		$this->addColumn(
            'total_no_of_orders_with_partial_payment',
            [
                'header' => __('No of Partially Paid orders placed'),
                'index' => 'total_no_of_orders_with_partial_payment',
				'total'     => 'sum',
                'sortable' => false,
				'filter'  => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
       $this->addColumn(
            'partial_total_invoiced_amount',
            [
                'header' => __('Order value of Partially paid orders'),
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'index' => 'total_amount_of_partial_payment_orders',
                'total' => 'sum',				
                'sortable' => false,
				'filter'  => false,
                'rate' => $rate,
                'header_css_class' => 'col-invoiced',
                'column_css_class' => 'col-invoiced'
            ]
        );
		
       $this->addColumn(
            'orders_count',
            [
                'header' => __('No of Fully paid orders placed'),
                'index' => 'total_no_of_orders_without_partial_payment',
                'type' => 'number',
                'total' => 'sum',				
                'sortable' => false,
				'filter'  => false,
                'header_css_class' => 'col-orders',
                'column_css_class' => 'col-orders'
            ]
        );
		
       $this->addColumn(
            'total_invoiced_amount',
            [
                'header' => __('Order value of fully paid orders'),
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'index' => 'total_amount_of_non_partial_payment_orders',
                'total' => 'sum',				
                'sortable' => false,
				'filter'  => false,
                'rate' => $rate,
                'header_css_class' => 'col-invoiced',
                'column_css_class' => 'col-invoiced'
            ]
        );
        $this->addExportType('*/*/exportRevenueCsv', __('CSV'));
        $this->addExportType('*/*/exportRevenueExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}