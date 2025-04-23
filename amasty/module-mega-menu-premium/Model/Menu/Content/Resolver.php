<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\Menu\Content;

use Amasty\MegaMenuPremium\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Content\Resolver as ResolverLite;
use Magento\Framework\Data\Tree\Node;

class Resolver extends ResolverLite
{
    public function resolve(Node $node): ?string
    {
        $content = $node->getData(ItemInterface::MOBILE_CONTENT);

        return $content && $this->isDirectivesExists($content) ? $this->parseWysiwyg($content) : $content;
    }
}
