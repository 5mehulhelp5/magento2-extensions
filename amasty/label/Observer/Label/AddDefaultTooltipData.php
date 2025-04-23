<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Observer\Label;

use Amasty\Label\Model\Label;
use Amasty\Label\Model\Label\Parts\LabelTooltip;
use Amasty\Label\Model\Label\Parts\LabelTooltipFactory;
use Amasty\Label\Model\Source\TooltipStatus;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddDefaultTooltipData implements ObserverInterface
{
    /**
     * @var LabelTooltipFactory
     */
    private $labelTooltipFactory;

    public function __construct(
        LabelTooltipFactory $labelTooltipFactory
    ) {
        $this->labelTooltipFactory = $labelTooltipFactory;
    }

    public function execute(Observer $observer): void
    {
        /** @var Label $label */
        $label = $observer->getEvent()->getData('label_entity');

        if (null === $label->getExtensionAttributes()->getLabelTooltip()) {
            $label->getExtensionAttributes()
                ->setLabelTooltip($this->createDefaultTooltip());
        }
    }

    private function createDefaultTooltip(): LabelTooltip
    {
        return $this->labelTooltipFactory->create([
            'data' => [
                LabelTooltip::STATUS => TooltipStatus::DISABLED,
                LabelTooltip::COLOR => '',
                LabelTooltip::TEXT_COLOR => '',
                LabelTooltip::TEXT => ''
            ]
        ]);
    }
}
