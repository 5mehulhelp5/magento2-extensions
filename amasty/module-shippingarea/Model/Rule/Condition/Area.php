<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Condition;

class Area extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Amasty\ShippingArea\Model\ResourceModel\Area\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\ShippingArea\Model\AreaRepository
     */
    private $areaRepository;

    /**
     * @var \Amasty\ShippingArea\Model\Validator\ValidatorComposite
     */
    private $areaValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Amasty\ShippingArea\Model\ResourceModel\Area\CollectionFactory $collectionFactory,
        \Amasty\ShippingArea\Model\AreaRepository $areaRepository,
        \Amasty\ShippingArea\Model\Validator\ValidatorComposite $areaValidator,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $context->getLogger();
        $this->collectionFactory = $collectionFactory;
        $this->areaRepository = $areaRepository;
        $this->areaValidator = $areaValidator;
    }

    /**
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $options = $this->collectionFactory->create()->addActiveFilter()->toOptionArray();
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $value = $this->getValueElementHtml() ?? '';

        return $this->getTypeElementHtml()
            . __(sprintf(__('Shipping Areas') . ' %s: %s', $this->getOperatorElementHtml(), $value))
            . $this->getRemoveLinkHtml();
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Quote\Model\Quote\Address $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $return = false;
        try {
            $area = $this->areaRepository->getById($this->getValue());
            if (!$area->getIsEnabled()) {
                $this->ariaDisabledWarning();
                return true;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->ariaDisabledWarning();
            return true;
        }

        if ($this->areaValidator->isValid($area, $model)) {
            $return = $this->validateAttribute($area->getId());
        } else {
            $return = $this->validateAttribute(0);
        }

        return $return;
    }

    /**
     * log warning
     */
    protected function ariaDisabledWarning()
    {
        $this->logger->warning(sprintf('Error while condition validation: '
            . 'Shipping Area with specified ID "%s" not found or the Area is disabled. '
            . 'You should modify a Rule(s) or enable the Area', $this->getValue()));
    }
}
