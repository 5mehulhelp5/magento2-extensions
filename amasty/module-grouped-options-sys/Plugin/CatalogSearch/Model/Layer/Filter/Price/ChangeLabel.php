<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Plugin\CatalogSearch\Model\Layer\Filter\Price;

use Amasty\GroupedOptions\Plugin\CatalogSearch\Model\Layer\Filter\ChangeDecimalLabels;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\CatalogSearch\Model\Layer\Filter\Price as PriceFilter;

class ChangeLabel
{
    /**
     * @var ChangeDecimalLabels
     */
    private $changeDecimalLabels;

    public function __construct(ChangeDecimalLabels $changeDecimalLabels)
    {
        $this->changeDecimalLabels = $changeDecimalLabels;
    }

    /**
     * @param PriceFilter $subject
     * @param FilterItem[] $items
     * @return FilterItem[]
     */
    public function afterGetItems(PriceFilter $subject, array $items): array
    {
        return $this->changeDecimalLabels->execute(
            (int) $subject->getAttributeModel()->getAttributeId(),
            $items
        );
    }
}
