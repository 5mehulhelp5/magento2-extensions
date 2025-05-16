<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Model\Layer\Filter\Resolver;

use Amasty\Shopby\Helper\FilterSetting as FilterSettingHelper;
use Amasty\Shopby\Model\ConfigProvider;
use Amasty\Shopby\Model\Layer\Filter\Decimal;
use Amasty\Shopby\Model\Layer\Filter\IsNew;
use Amasty\Shopby\Model\Layer\Filter\OnSale;
use Amasty\Shopby\Model\Layer\Filter\Price;
use Amasty\Shopby\Model\Layer\Filter\Rating;
use Amasty\Shopby\Model\Layer\Filter\Stock;
use Amasty\Shopby\Model\Source\PositionLabel;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Model\FilterSetting\IsMultiselect;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class FilterSettingResolver
{
    /**
     * @var FilterSettingInterface[]
     */
    private $filterSetting = [];

    /**
     * @var FilterSettingHelper
     */
    private $settingHelper;

    /**
     * @var IsMultiselect
     */
    protected $isMultiselect;

    public function __construct(
        FilterSettingHelper $settingHelper,
        ?ConfigProvider $configProvider, // @deprecated dependency to config not needed any more
        IsMultiselect $isMultiselect
    ) {
        $this->settingHelper = $settingHelper;
        $this->isMultiselect = $isMultiselect;
    }

    public function isMultiselectAllowed(FilterInterface $filter): bool
    {
        switch (true) {
            case $filter instanceof Stock:
            case $filter instanceof Rating:
            case $filter instanceof OnSale:
            case $filter instanceof IsNew:
                $isMultiselectAllowed = false;
                break;
            case $filter instanceof Price:
            case $filter instanceof Decimal:
                $isMultiselectAllowed = true;
                break;
            default:
                $isMultiselectAllowed = $this->isMultiSelect($filter);
        }

        return $isMultiselectAllowed;
    }

    /**
     * @deprecated proxy method that doing nothing uniq
     * @see \Amasty\ShopbyBase\Model\FilterSetting\FilterResolver::resolveByFilter
     */
    public function getFilterSetting(FilterInterface $filter): FilterSettingInterface
    {
        return $this->settingHelper->getSettingByLayerFilter($filter);
    }

    /**
     * @deprecated proxy method that doing nothing uniq
     * @see \Amasty\Shopby\Model\ConfigProvider::isSingleChoiceMode
     */
    public function isSingleChoiceMode(): bool
    {
        return ObjectManager::getInstance()->get(\Amasty\Shopby\Model\ConfigProvider::class)->isSingleChoiceMode();
    }

    private function isMultiSelect(FilterInterface $filter): bool
    {
        $filterSetting = $this->getFilterSetting($filter);

        return $this->isMultiselect->execute(
            $filterSetting->getAttributeCode(),
            $filterSetting->isMultiselect(),
            $filterSetting->getDisplayMode()
        );
    }
}
