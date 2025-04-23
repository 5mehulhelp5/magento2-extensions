<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Model\DataProvider;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * Return an empty array as data as we populate through the browser
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }
}
