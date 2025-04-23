<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Api;

/**
 * @api
 */
interface LayoutTypeInterface
{
    /**
     * Get element config
     *
     * @return array
     */
    public function getElementConfig();
}
