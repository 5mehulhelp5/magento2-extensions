<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\Utilities\Brand;

use Amasty\Reports\Model\ConfigProvider;
use Amasty\Reports\Model\Source\Attributes;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config as EavConfig;

class GetBrandAttributeCode
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    public function __construct(ConfigProvider $configProvider, EavConfig $eavConfig)
    {
        $this->configProvider = $configProvider;
        $this->eavConfig = $eavConfig;
    }

    public function execute(): string
    {
        $attributeCode = $this->configProvider->getReportBrand();

        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
        if (!in_array($attribute->getFrontendInput(), Attributes::ALLOWED_FRONTEND_TYPES, true)) {
            return '';
        }

        return $attribute->getAttributeId() ? $attribute->getAttributeCode() : '';
    }
}
