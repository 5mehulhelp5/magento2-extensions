<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model;

use Amasty\Base\Model\Config;

class ConfigProvider extends Config
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_label/';

    public const MAX_LABELS = 999;

    public const XML_PATH_DEFAULT_STOCK_LABEL = 'stock_status/default_label';
    public const XML_PATH_USE_IS_NEW_RANGES = 'new/is_new';
    public const XML_PATH_USE_CREATION_DATE = 'new/creation_date';
    public const XML_PATH_CREATION_DATE_THRESHOLD = 'new/days';
    public const XML_PATH_MIN_DISCOUNT_ABSOLUTE = 'on_sale/sale_min';
    public const XML_PATH_MIN_DISCOUNT_PERCENTAGE = 'on_sale/sale_min_percent';
    public const XML_PATH_ROUNDING_TYPE = 'on_sale/rounding';
    public const XML_PATH_MAX_LABEL_COUNT = 'display/max_labels';
    public const XML_PATH_SHOW_SEVERAL_LABELS_ON_SAME_PATH = 'display/show_several_on_place';
    public const XML_PATH_MARGIN_BETWEEN = 'display/margin_between';
    public const XML_PATH_LABEL_ALIGNMENT = 'display/labels_alignment';
    public const XML_PATH_PRODUCT_CONTAINER_PATH = 'display/product';
    public const XML_PATH_LIST_CONTAINER_PATH = 'display/category';
    public const XML_PATH_DEFAULT_LABEL_ID = 'stock_status/default_label';
    public const XML_PATH_OUT_OF_STOCK_ONLY = 'stock_status/out_of_stock_only';
    public const XML_PATH_HIDE_IF_LABEL_HAS_ZERO_LABEL = 'display/hide_if_zero_label';
    public const XML_PATH_RECENTLY_VIEWED_SCOPE = 'catalog/recently_products/scope';
    public const XML_PATH_RECENTLY_VIEWED_LIFETIME = 'catalog/recently_products/recently_viewed_lifetime';

    public function getDefaultStockLabelId(): ?int
    {
        $id = $this->getValue(self::XML_PATH_DEFAULT_STOCK_LABEL);

        return $id === null ? null : (int) $id;
    }

    public function useNewFromToRanges(): bool
    {
        return (bool) $this->getValue(self::XML_PATH_USE_IS_NEW_RANGES);
    }

    public function useCreationDateForNew(): bool
    {
        return (bool) $this->getValue(self::XML_PATH_USE_CREATION_DATE);
    }

    public function getIsNewDaysThreshold(): int
    {
        return (int) $this->getValue(self::XML_PATH_CREATION_DATE_THRESHOLD);
    }

    public function getMinDiscountAbsolute(): float
    {
        return (float) $this->getValue(self::XML_PATH_MIN_DISCOUNT_ABSOLUTE);
    }

    public function getMinDiscountPercentage(): float
    {
        return (float) $this->getValue(self::XML_PATH_MIN_DISCOUNT_PERCENTAGE);
    }

    public function getRoundingFunctionName(): string
    {
        $functionName = $this->getValue(self::XML_PATH_ROUNDING_TYPE);

        return $functionName ?: 'round';
    }

    public function getMaxLabels(): int
    {
        $maxLabels = $this->getValue(self::XML_PATH_MAX_LABEL_COUNT);

        return $maxLabels === null ? self::MAX_LABELS : (int) $maxLabels;
    }

    public function isShowSeveralOnPlace(): bool
    {
        return (bool) $this->isSetFlag(self::XML_PATH_SHOW_SEVERAL_LABELS_ON_SAME_PATH);
    }

    public function getMarginBetween(): int
    {
        return (int) $this->getValue(self::XML_PATH_MARGIN_BETWEEN);
    }

    public function getLabelAlignment(): int
    {
        return (int) $this->getValue(self::XML_PATH_LABEL_ALIGNMENT);
    }

    public function getProductContainerPath(): string
    {
        return (string) $this->getValue(self::XML_PATH_PRODUCT_CONTAINER_PATH);
    }

    public function getProductListContainerPath(): string
    {
        return (string) $this->getValue(self::XML_PATH_LIST_CONTAINER_PATH);
    }

    public function getDefaultOutOfStockLabelId(): int
    {
        return (int) $this->getValue(self::XML_PATH_DEFAULT_LABEL_ID);
    }

    public function isOutOfStockLabelEnabled(): bool
    {
        return (bool) $this->getValue(self::XML_PATH_OUT_OF_STOCK_ONLY);
    }

    public function isHideLabelWithZeroValue(): bool
    {
        return (bool) $this->getValue(self::XML_PATH_HIDE_IF_LABEL_HAS_ZERO_LABEL);
    }

    public function getRecentlyViewedScope(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_RECENTLY_VIEWED_SCOPE);
    }

    public function getRecentlyViewedLifetime(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_RECENTLY_VIEWED_LIFETIME);
    }
}
