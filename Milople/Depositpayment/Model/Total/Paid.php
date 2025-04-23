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
namespace Milople\Depositpayment\Model\Total;

use \AllowDynamicProperties;

#[AllowDynamicProperties]
class Paid extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null; 
	private $logger;

    public function __construct(
		\Milople\Depositpayment\Model\Calculation $calculation,
		\Magento\Quote\Model\QuoteValidator $quoteValidator
	)
    {
		$this->model_calculation = $calculation;
        $this->quoteValidator = $quoteValidator;
    }
  	public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $paid = $this->model_calculation->getPayingNow($quote);
        $total->setPaid($paid);
        $total->setBasePaid($paid);
        return $this;
    } 
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
   	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {		
		$paid = $this->model_calculation->getPayingNow($quote);
		$total->setPaidAmount($paid);
        $total->setBasePaidAmount($paid);
        return [
            'code' => 'paid',
            'title' => 'Down Payment',
            'value' => $paid
        ];
    }
}