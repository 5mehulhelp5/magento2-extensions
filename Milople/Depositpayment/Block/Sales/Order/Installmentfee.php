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
class Installmentfee extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
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
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
	public function initTotals()
	{
		$parent = $this->getParentBlock();
		$source = $parent->getSource();

		if($source->getInstallmentFee() > 0)
		{
			$installmentFee = new \Magento\Framework\DataObject(
				[
					'code' => 'installment_fee',
					'strong' => true,
					'value' => $source->getInstallmentFee(),
					'base_value' => $source->getBaseInstallmentFee(),
					'area'=> 'footer',
					'label' => $this->calculationModel->getInstallmentLabel()
				]
			);

			$parent->addTotal($installmentFee, 'installment_fee');
		}
		return $this;
	}
}