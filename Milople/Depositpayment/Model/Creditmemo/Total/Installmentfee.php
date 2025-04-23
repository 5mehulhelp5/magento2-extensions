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
namespace Milople\Depositpayment\Model\Creditmemo\Total;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Installmentfee extends AbstractTotal
{
	 /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $creditmemo->setInstallmentFee(0);
        $creditmemo->setBaseInstallmentFee(0);
        $amount = $creditmemo->getOrder()->getInstallmentFee();
        $creditmemo->setInstallmentFee($amount);
        $amount = $creditmemo->getOrder()->getBaseInstallmentFee();
        $creditmemo->setBaseInstallmentFee($amount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getInstallmentFee());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getBaseInstallmentFee());
        return $this;
    }
}