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
namespace Milople\Depositpayment\Model\Invoice\Total;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Installmentfee extends AbstractTotal
{
	/**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setInstallmentFee(0);
        $invoice->setBaseInstallmentFee(0);
        $amount = $invoice->getOrder()->getInstallmentFee();
        $invoice->setInstallmentFee($amount);
        $amount = $invoice->getOrder()->getBaseInstallmentFee();
        $invoice->setBaseInstallmentFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getInstallmentFee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getBaseInstallmentFee());
        return $this;
    }
}