<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Area;

class PostcodeDataProcessor
{
    private const RESTRICTED_CHARS = [
        "\r\n",
        "\n",
        "\r"
    ];

    public function process(array $postcodeData): array
    {
        foreach ($postcodeData as $key => &$zipRow) {
            $zipRow['zip_from'] = $this->sanitizeZip($zipRow['zip_from']);
            $zipRow['zip_to'] = $this->sanitizeZip($zipRow['zip_to']);
            if (isset($zipRow['delete']) && $zipRow['delete']) {
                unset($postcodeData[$key]);
            }
        }

        return $postcodeData;
    }

    private function sanitizeZip(string $zipRow): string
    {
        return str_replace(self::RESTRICTED_CHARS, '', $zipRow);
    }
}
