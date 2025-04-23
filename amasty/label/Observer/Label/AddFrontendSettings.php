<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Observer\Label;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Parts\FrontendSettings\Query\GetFrontendSettings;
use Amasty\Label\Model\ResourceModel\Label\Collection as LabelCollection;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFrontendSettings implements ObserverInterface
{
    /**
     * @var GetFrontendSettings
     */
    private $getFrontendSettings;

    public function __construct(GetFrontendSettings $getFrontendSettings)
    {
        $this->getFrontendSettings = $getFrontendSettings;
    }

    public function execute(EventObserver $observer): void
    {
        /** @var LabelCollection $labelCollection */
        $labelCollection = $observer->getEvent()->getData('collection');

        $labelIds = array_map(function ($labelId) {
            return (int)$labelId;
        }, $labelCollection->getAllIds());

        $frontendSettingsByLabelId = $this->getFrontendSettings->execute($labelIds, $labelCollection->getMode());
        /** @var LabelInterface $label */
        foreach ($labelCollection->getItems() as $label) {
            if ($label->getExtensionAttributes()->getFrontendSettings() === null) {
                $label->getExtensionAttributes()->setFrontendSettings($frontendSettingsByLabelId[(int)$label->getId()]);
            }
        }
    }
}
