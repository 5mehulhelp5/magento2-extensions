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
namespace Milople\Depositpayment\Block\Adminhtml\Sales\Order\Invoice;

class Paid extends \Magento\Framework\View\Element\Template
{
	/**
     * OrderFee constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Milople\Depositpayment\Model\Calculation $calculationModel,
        array $data = []
    ) {
        $this->calculationModel = $calculationModel;
        parent::__construct($context, $data);
    }
	 /**
     * Get data (totals) source model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
    /**
     * Initialize payment fee totals
     *
     * @return $this
     */
    public function initTotals()
    {
		\Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug("*********(###)**********");
        $this->getParentBlock();
        $this->getInvoice();
		$order = $this->getInvoice()->getOrder();
        $this->getSource();
        if(!$order->getPaidAmount()) {
            return $this;
        }
		\Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($order->getPaidAmount());
        $paid = new \Magento\Framework\DataObject(
            [
                'code' => 'paid',
                'strong' => false,                
				'value' => $order->getPaidAmount(),
                'base_value' => $order->getBasePaidAmount(),
                'label' => "Paid",
            ]
        );
        $this->getParentBlock()->addTotalBefore($paid, 'grand_total');
        return $this;
    }
}