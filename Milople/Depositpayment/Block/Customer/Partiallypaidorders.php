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
namespace Milople\Depositpayment\Block\Customer;
class Partiallypaidorders extends \Magento\Framework\View\Element\Template
{
     protected $_gridFactory;
	protected $orderCollectionFactory;
     public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
       	\Milople\Depositpayment\Model\PartialpaymentOrders $partiallypaidordersFactory,	
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
     ) {
        $this->partiallypaidordersFactory = $partiallypaidordersFactory;
		$this->priceHelper = $pricingHelper; // Instance of Pricing Helper
        $this->customerSession = $customerSession;
        $customerId=$this->customerSession->getCustomer()->getId();
        parent::__construct($context, $data);
		$collection = $this->partiallypaidordersFactory->getCollection();
		$collection->getSelect()->joinLeft(
					['order1'=>$collection->getTable('sales_order')],
					'main_table.order_id = order1.entity_id',
					['increment_id'=>'order1.increment_id',
					 'firstname'=>'order1.customer_firstname',
					 'lastname'=>'order1.customer_lastname',
					 'email'=>'order1.customer_email',
					 'order_date'=>'order1.created_at',
					 'order_status'=>'order1.status'
					])->where(
                     "order1.customer_id = " .$customerId);
       
      
		$collection->setOrder('partial_payment_id','DESC');
		$this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('Deposit Payment Orders'));
    }
  
    protected function _prepareLayout()
    {
	    
        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection 
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'partiallypaidorders.grid.record.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }
  
    /**
     * @return string
     */
    // method for get pager html
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}