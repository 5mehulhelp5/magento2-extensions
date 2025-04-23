<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Pricing;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class GetProductSpecialPrice
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;
    }

    public function execute(ProductInterface $product): float
    {
        switch ($product->getTypeId()) {
            case BundleType::TYPE_CODE:
            case Configurable::TYPE_CODE:
                $specialPrice =  $this->getPrice($product, FinalPrice::PRICE_CODE);
                break;
            default:
                $finalPrice = $this->getPrice($product, FinalPrice::PRICE_CODE);

                if ($this->isSpecialPriceActive($product)) {
                    $specialPrice = $this->getPrice($product, SpecialPrice::PRICE_CODE);
                    if (!$specialPrice) {
                        $specialPrice = $this->getPrice($product, RegularPrice::PRICE_CODE);
                    }
                    $specialPrice = min($finalPrice, $specialPrice);
                } else {
                    $specialPrice = $finalPrice;
                }
        }

        return (float) $specialPrice;
    }

    /**
     * @param ProductInterface $product
     * @param string $code
     *
     * @return float|string|false
     */
    private function getPrice(ProductInterface $product, string $code)
    {
        return $product->getPriceInfo()->getPrice($code)->getAmount()->getValue();
    }

    private function isSpecialPriceActive(ProductInterface $product): bool
    {
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();

        return ($specialFromDate || $specialToDate)
            && $this->timezone->isScopeDateInInterval(
                $product->getStoreId(),
                $specialFromDate,
                $specialToDate
            );
    }
}
