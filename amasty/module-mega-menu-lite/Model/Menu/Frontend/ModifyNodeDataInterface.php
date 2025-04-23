<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Magento\Framework\Data\Tree\Node;

interface ModifyNodeDataInterface
{
    public function execute(Node $node, array $data): array;
}
