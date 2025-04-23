<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Text\Processors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Text\ProcessorInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ProductAttributesProcessor implements ProcessorInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ProductResource
     */
    private $productResource;

    public function __construct(
        TimezoneInterface $timezone,
        ProductResource $productResource
    ) {
        $this->timezone = $timezone;
        $this->productResource = $productResource;
    }

    public function getAcceptableVariables(): array
    {
        return [
            self::ALL_VARIABLES_FLAG
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param string $variable
     * @param LabelInterface $label
     * @param ProductInterface $product
     * @return string
     * @throws \Exception
     */
    public function getVariableValue(string $variable, LabelInterface $label, ProductInterface $product): string
    {
        /** @var Product $product **/
        $str = 'ATTR:';
        $value = '';

        if (substr($variable, 0, strlen($str)) == $str) {
            $code  = trim(substr($variable, strlen($str)));

            $decimal = null;

            if (false !== strpos($code, ':')) {
                $temp = explode(':', $code);
                $code = $temp[0];
                $decimal = $temp[1];
            }

            if (!$code) {
                return '';
            }

            $attribute = $this->productResource->getAttribute($code);

            if ($product->hasData($code)) {
                $value = $product->getData($code);
            } else {
                $value = $this->productResource->getAttributeRawValue(
                    $product->getId(),
                    $code,
                    $product->getStoreId()
                );
            }

            if ($attribute && $attribute->usesSource()) {
                $value = $attribute->getSource()->getOptionText($value);
            }

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $value = (string) $value;
            if ($value && $decimal !== null && false !== strpos($value, '.')) {
                $temp = explode('.', $value);
                $value = $temp[0] . '.' . substr($temp[1], 0, (int)$decimal);
            }

            if ($value
                && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value)
                && strtotime($value) !== false
            ) {
                $value = $this->timezone->formatDateTime(
                    new \DateTime($value),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE
                );
            }
        }

        return (string) $value;
    }
}
