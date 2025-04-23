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

class PartialpaymentInstallments extends AbstractModel
{	
	/**     
	 * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
	 * @param ResourceModel\PartialpaymentInstallments\CollectionFactory $partialpaymentInstallmentsCollectionFactory
	 * @param \Milople\Depositpayment\Model\PartialpaymentOrders $partialpaymentOrderModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,        
		\Milople\Depositpayment\Model\ResourceModel\PartialpaymentInstallments\CollectionFactory $partialpaymentInstallmentsCollectionFactory,
		\Milople\Depositpayment\Model\PartialpaymentOrders $partialpaymentOrderModel,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
		array $data = []
    ) {        
        $this->partialpaymentInstallmentsCollectionFactory = $partialpaymentInstallmentsCollectionFactory;
		$this->partialpaymentOrderModel = $partialpaymentOrderModel;
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
		$this->_init('Milople\Depositpayment\Model\ResourceModel\PartialpaymentInstallments');
	}
	/**
     * Get Partialpayment Installments collection model populated with data
     *
     * @param array $filters
     * @return \Milople\Depositpayment\Model\PartialpaymentInstallments\Collection
     */
    protected function getPartialpaymentInstallmentsCollection(array $filters = [])
    {
        /** @var \Milople\Depositpayment\Model\ResourceModel\PartialpaymentInstallments\Collection $partialpaymentInstallmentsCollection */
        $partialpaymentsInstallmentsCollection = $this->partialpaymentInstallmentsCollectionFactory->create();
        foreach ($filters as $field => $condition) {
            $partialpaymentsInstallmentsCollection->addFieldToFilter($field, $condition);
        }
        return $partialpaymentsInstallmentsCollection->load();
    }
	/**
     * Get PartialpaymentInstallments Collection by Partialpaymentpro Order Id
     *
     * @param int $PartialPaymentId     
     * @return \Milople\Depositpayment\Model\PartialpaymentInstallments\Collection
     */
    public function getInstallmentsByPartialPaymentId($PartialPaymentId)
    {
        return $partialInstallmentsCollection = $this->getPartialpaymentInstallmentsCollection(
            [
                'partial_payment_id' => $PartialPaymentId                
            ]
        );
        
    }
	/**
     * Get PartialpaymentInstallments Collection by Sale Order Id
     *
     * @param string $orderId     
     * @return \Milople\Depositpayment\Model\PartialpaymentInstallments\Collection
     */
    public function getInstallmentsByOrderId($orderId)
    {
		$PartialPaymentId = $this->partialpaymentOrderModel->getPartialIdByOrderId($orderId);
        return $this->getInstallmentsByPartialPaymentId($PartialPaymentId);
        
    }
}