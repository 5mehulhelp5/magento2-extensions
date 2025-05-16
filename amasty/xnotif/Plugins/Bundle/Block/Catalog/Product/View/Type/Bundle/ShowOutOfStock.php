<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\Bundle\Block\Catalog\Product\View\Type\Bundle;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle;
use Magento\Bundle\Model\Option;

class ShowOutOfStock
{
    public const NATIVE_STOCK_STATUS = 'amasty_native_is_salable';

    /**
     * Emulate stock status for out of stock selections.
     *
     * @param Bundle $subject
     * @param Option $option
     * @return array
     */
    public function beforeGetOptionHtml(Bundle $subject, Option $option): array
    {
        foreach ($option->getSelections() as $selection) {
            if ($selection->isSalable()) {
                $selection->setData(self::NATIVE_STOCK_STATUS, true);
            } else {
                $selection->setData('salable', true);
                $selection->setData(self::NATIVE_STOCK_STATUS, false);

                $name = $selection->getName();
                $name .= ' (' . __('Out of Stock') . ')';
                $selection->setData('name', $name);
            }
        }

        return [$option];
    }
}
