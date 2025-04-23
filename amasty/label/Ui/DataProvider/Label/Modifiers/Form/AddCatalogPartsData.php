<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\ResourceModel\Label\Collection as LabelsCollection;
use Amasty\Label\Model\ResourceModel\Label\Grid\Collection as LabelsGridCollection;
use Amasty\Label\Model\ResourceModel\Label\Grid\CollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddCatalogPartsData implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        ?CollectionFactory $collectionFactory, // @deprecated
        LabelRegistry $labelRegistry
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->labelRegistry = $labelRegistry;
    }

    protected function executeIfLabelExists(int $labelId, array $data): array
    {
        $labelData = $data[$labelId] ?? [];
        $labelData = array_merge($labelData, ...$this->getCatalogPartsData($this->labelRegistry->getCurrentLabel()));
        $data[$labelId] = $labelData;

        return $data;
    }

    private function getCatalogPartsData(LabelInterface $label): array
    {
        $catalogPartsData = [];
        if (!$label->getExtensionAttributes()->getFrontendSettings()) {
            return $catalogPartsData;
        }

        foreach ($label->getExtensionAttributes()->getFrontendSettings() as $frontendSetting) {
            $prefix = $frontendSetting->getType() === LabelsCollection::MODE_LIST
                ? LabelsGridCollection::CATEGORY_PREFIX
                : LabelsGridCollection::PRODUCT_PREFIX;
            $catalogPartData = [];
            foreach ($frontendSetting->getData() as $key => $value) {
                $catalogPartData[$prefix . '_' . $key] = $value;
            }
            $catalogPartsData[] = $catalogPartData;
        }

        return $catalogPartsData;
    }
}
