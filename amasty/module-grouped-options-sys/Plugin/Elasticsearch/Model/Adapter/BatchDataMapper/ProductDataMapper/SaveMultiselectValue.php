<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Plugin\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

use Amasty\GroupedOptions\Model\GroupAttr\Query\GetRelatedOptions;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

class SaveMultiselectValue
{
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var GetRelatedOptions
     */
    private $getRelatedOptions;

    public function __construct(EavConfig $eavConfig, GetRelatedOptions $getRelatedOptions)
    {
        $this->eavConfig = $eavConfig;
        $this->getRelatedOptions = $getRelatedOptions;
    }

    /**
     * Parse multiselect values for grouped options.
     *
     * Magento doesn't support multiselect options for non multiselect attribute.
     *
     * @see ProductDataMapper::prepareAttributeValues
     *
     * @param ProductDataMapper $subject
     * @param array $result
     * @param array $documentData
     * @return array
     *
     * @see ProductDataMapper::map
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterMap(ProductDataMapper $subject, $result, array $documentData)
    {
        $groupAttributeIds = array_keys($this->getRelatedOptions->execute());
        foreach ($documentData as $productId => $indexData) {
            foreach ($groupAttributeIds as $attributeId) {
                if (!isset($indexData[$attributeId]) || !isset($result[$productId])) {
                    continue;
                }

                $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeId);
                if ($attribute->getFrontendInput() === 'multiselect'
                    || !isset($result[$productId][$attribute->getAttributeCode()])
                ) {
                    continue;
                }

                $result[$productId][$attribute->getAttributeCode()] = $this->prepareMultiselectValues(
                    $indexData[$attributeId]
                );
            }
        }

        return $result;
    }

    private function prepareMultiselectValues(array $values): array
    {
        return \array_values(\array_unique(\array_merge(
            ...\array_map(
                function (string $value) {
                    return \explode(',', $value);
                },
                $values
            )
        )));
    }
}
