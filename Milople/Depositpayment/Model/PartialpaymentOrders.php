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
namespace Milople\Depositpayment\Model;

use Magento\Framework\Model\AbstractModel;
use Milople\Depositpayment\Api\Data\GridInterface;

class PartialpaymentOrders extends AbstractModel implements GridInterface
{
    /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'partial_payment_orders';
 
    /**
     * @var string
     */
    protected $_cacheTag = 'partial_payment_orders';
 
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'partial_payment_orders';
	/**     
	 * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
	 * @param ResourceModel\PartialpaymentOrders\CollectionFactory $partialpaymentOrderCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,        
		\Milople\Depositpayment\Model\ResourceModel\PartialpaymentOrders\CollectionFactory $partialpaymentOrderCollectionFactory,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
		array $data = []
    ) {        
        $this->partialpaymentOrderCollectionFactory = $partialpaymentOrderCollectionFactory;
		parent::__construct(
            $context,
            $registry,            
            $resource,
            $resourceCollection,
            $data
        );	
    }
	/**
	 * Define resource model
	 */
	protected function _construct()
	{
		$this->_init('Milople\Depositpayment\Model\ResourceModel\PartialpaymentOrders');
	}

    /**
     * Get PartialPaymentId.
     *
     * @return int
     */
    public function getPartialPaymentId()
    {
        return $this->getData(self::PARTIAL_PAYMENT_ID);
    }
 
    /**
     * Set PartialPaymentId.
     */
    public function setPartialPaymentId($partial_payment_id)
    {
        return $this->setData(self::PARTIAL_PAYMENT_ID, $partial_payment_id);
    }

    /**
     * Get OrderId.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }
 
    /**
     * Set OrderId.
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Get IsPreordered.
     *
     * @return int
     */
    public function getIsPreordered()
    {
        return $this->getData(self::IS_PREORDERED);
    }
 
    /**
     * Set IsPreordered.
     */
    public function setIsPreordered($is_preordered)
    {
        return $this->setData(self::IS_PREORDERED, $is_preordered);
    }

    /**
     * Get TotalInstallments.
     *
     * @return int
     */
    public function getTotalInstallments()
    {
        return $this->getData(self::TOTAL_INSTALLMENTS);
    }
 
    /**
     * Set TotalInstallments.
     */
    public function setTotalInstallments($total_installments)
    {
        return $this->setData(self::TOTAL_INSTALLMENTS, $total_installments);
    }

    /**
     * Get PaidInstallments.
     *
     * @return int
     */
    public function getPaidInstallments()
    {
        return $this->getData(self::PAID_INSTALLMENTS);
    }
 
    /**
     * Set PaidInstallments.
     */
    public function setPaidInstallments($paid_installments)
    {
        return $this->setData(self::PAID_INSTALLMENTS, $paid_installments);
    }

    /**
     * Get RemainingInstallments.
     *
     * @return int
     */
    public function getRemainingInstallments()
    {
        return $this->getData(self::REMAINING_INSTALLMENTS);
    }
 
    /**
     * Set RemainingInstallments.
     */
    public function setRemainingInstallments($remaining_installments)
    {
        return $this->setData(self::REMAINING_INSTALLMENTS, $remaining_installments);
    }

    /**
     * Get TotalAmount.
     *
     * @return int
     */
    public function getTotalAmount()
    {
        return $this->getData(self::TOTAL_AMOUNT);
    }
 
    /**
     * Set TotalAmount.
     */
    public function setTotalAmount($total_amount)
    {
        return $this->setData(self::TOTAL_AMOUNT, $total_amount);
    }

    /**
     * Get PaidAmount.
     *
     * @return int
     */
    public function getPaidAmount()
    {
        return $this->getData(self::PAID_AMOUNT);
    }
 
    /**
     * Set PaidAmount.
     */
    public function setPaidAmount($paid_amount)
    {
        return $this->setData(self::PAID_AMOUNT, $paid_amount);
    }

    /**
     * Get RemainingAmount.
     *
     * @return int
     */
    public function getRemainingAmount()
    {
        return $this->getData(self::REMAINING_AMOUNT);
    }
 
    /**
     * Set RemainingAmount.
     */
    public function setRemainingAmount($remaining_amount)
    {
        return $this->setData(self::REMAINING_AMOUNT, $remaining_amount);
    }



	/**
     * Get PartialpaymentOrder Id by Sales Order Id
     *
     * @param string $orderId     
     * @return int 
     */
    public function getPartialIdByOrderId($orderId)
    {
        $partialOrderCollection = $this->getPartialpaymentOrderCollection(
            [
                'order_id' => $orderId                
            ]
        );
        return $partialOrderCollection->getFirstItem()->getPartialPaymentId();
    }
	/**
     * Load PartialpaymentOrder by Sales Order Id
     *
     * @param string $orderId     
     * @return \Milople\Depositpayment\Model\PartialpaymentOrders
     */
    public function loadByOrderId($orderId)
    {
        $partialOrderCollection = $this->getPartialpaymentOrderCollection(
            [
                'order_id' => $orderId                
            ]
        );
        return $partialOrderCollection->getFirstItem();
    }

    /**
     * Get Partialpayment Order collection model populated with data
     *
     * @param array $filters
     * @return \Milople\Depositpayment\Model\PartialpaymentOrders\Collection
     */
    protected function getPartialpaymentOrderCollection(array $filters = [])
    {
        $partialpaymentsOrderCollection = $this->partialpaymentOrderCollectionFactory->create();
        foreach ($filters as $field => $condition) {
            $partialpaymentsOrderCollection->addFieldToFilter($field, $condition);
        }
        return $partialpaymentsOrderCollection->load();
    }
}