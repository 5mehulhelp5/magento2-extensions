<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Api\UrlBuilder;

interface AdapterInterface
{
    /**
     * @param   string|null $routePath
     * @param   array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null);
}
