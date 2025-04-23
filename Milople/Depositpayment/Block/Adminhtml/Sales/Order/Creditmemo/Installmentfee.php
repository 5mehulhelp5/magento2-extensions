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
namespace Milople\Depositpayment\Block\Adminhtml\Sales\Order\Creditmemo;

class Installmentfee extends \Magento\Framework\View\Element\Template
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
        $this->getParentBlock();
        $this->getInvoice();
        $this->getSource();
        if(!$this->getSource()->getInstallmentFee()) {
            return $this;
        }
        $total = new \Magento\Framework\DataObject(
            [
                'code' => 'installment_fee',
                'value' => $this->getSource()->getInstallmentFee(),
                'label' => $this->calculationModel->getInstallmentLabel(),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        return $this;
    }
}