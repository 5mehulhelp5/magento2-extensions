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
namespace Milople\Depositpayment\Api\Data;

interface GridInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const PARTIAL_PAYMENT_ID = 'partial_payment_id';
    const ORDER_ID = 'order_id';
    const IS_PREORDERED = 'is_preordered';
    const TOTAL_INSTALLMENTS = 'total_installments';
    const PAID_INSTALLMENTS = 'paid_installments';
    const REMAINING_INSTALLMENTS = 'remaining_installments';
    const TOTAL_AMOUNT = 'total_amount';
    const PAID_AMOUNT = 'paid_amount';
    const REMAINING_AMOUNT = 'remaining_amount';

    /**
     * Get PartialPaymentId.
     *
     * @return int
     */
    public function getPartialPaymentId();

    /**
     * Set PartialPaymentId.
     */
    public function setPartialPaymentId($partial_payment_id);

    /**
     * Get OrderId.
     *
     * @return varchar
     */
    public function getOrderId();

    /**
     * Set OrderId.
     */
    public function setOrderId($order_id);

    /**
     * Get IsPreordered.
     *
     * @return varchar
     */
    public function getIsPreordered();

    /**
     * Set IsPreordered.
     */
    public function setIsPreordered($is_preordered);

    /**
     * Get TotalInstallments.
     *
     * @return varchar
     */
    public function getTotalInstallments();

    /**
     * Set TotalInstallments.
     */
    public function setTotalInstallments($total_installments);

    /**
     * Get PaidInstallments.
     *
     * @return varchar
     */
    public function getPaidInstallments();

    /**
     * Set PaidInstallments.
     */
    public function setPaidInstallments($paid_installments);

    /**
     * Get RemainingInstallments.
     *
     * @return varchar
     */
    public function getRemainingInstallments();

    /**
     * Set RemainingInstallments.
     */
    public function setRemainingInstallments($remaining_installments);

    /**
     * Get TotalAmount.
     *
     * @return varchar
     */
    public function getTotalAmount();

    /**
     * Set TotalAmount.
     */
    public function setTotalAmount($total_amount);
}