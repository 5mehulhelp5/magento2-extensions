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
class Remaining extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
   /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
	private $logger;
    protected $quoteValidator = null; 

    public function __construct(
		\Milople\Depositpayment\Model\Calculation $calculation,
		\Magento\Quote\Model\QuoteValidator $quoteValidator,
		\Psr\Log\LoggerInterface $logger
	)
    {
		$this->model_calculation = $calculation;
        $this->quoteValidator = $quoteValidator;
		$this->logger = $logger;		
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
		$remaining = $this->model_calculation->getAmountToBePaidLater($quote);
		$total->setRemaining($remaining);
        $total->setBaseRemaining($remaining);
        return [
            'code' => 'remaining',
            'title' => 'Amount To Be Paid Later',
            'value' => $remaining
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Remaining');
    }
}