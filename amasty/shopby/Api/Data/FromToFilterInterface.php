<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */
namespace Amasty\Shopby\Api\Data;

interface FromToFilterInterface
{
    /**
     * @return string[]
     */
    public function getFromToConfig(): array;
}
