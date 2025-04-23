<?php

namespace Milople\Depositpayment\Block;

use Magento\Framework\View\Element\Template;

class Total extends Template
{
    public function __construct(
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->setInitialFields();
    }

    public function setInitialFields()
    {
        if (!$this->getLabel()) {
            $this->setLabel(__('Order Packing Charges'));
        }
    }

    public function initTotals()
    {
        $this->getParentBlock()->addTotal(
            new \Magento\Framework\DataObject(
                [
                    'code' => 'custom',
                    'strong' => $this->getStrong(),
                    'value' => "100", // extension attribute field
                    'base_value' => "100",
                    'label' => __($this->getLabel()),
                ]
            ),
            $this->getAfter()
        );
        return $this;
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
}