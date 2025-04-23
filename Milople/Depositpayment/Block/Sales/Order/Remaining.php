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
namespace Milople\Depositpayment\Block\Sales\Order;
class Remaining extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
		 array $data = []
    ) {
        $this->_config = $taxConfig;
        parent::__construct($context, $data);
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
	public function initTotals()
	{
		$parent = $this->getParentBlock();
		$source = $parent->getSource();

		if($source->getBaseRemainingAmount() > 0)
		{
			$Remaining = new \Magento\Framework\DataObject(
				[
					'code' => 'partial_remaining',
					'strong' => true,
					'value' => $source->getRemainingAmount(),
					'base_value' => $source->getBaseRemainingAmount(),
					'area' => 'footer',
					'label' => __('Remaining'),
				]
			);
			$parent->addTotal($Remaining, 'Remaining');
		}
		return $this;
	}
}