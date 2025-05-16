<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Model\GroupAttr;

interface DataFactoryProviderInterface
{
    public function create(array $data = []): DataProvider;
}
