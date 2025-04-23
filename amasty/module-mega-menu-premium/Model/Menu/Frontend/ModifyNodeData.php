<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\Menu\Frontend;

use Amasty\MegaMenuLite\Model\Menu\Frontend\ModifyNodeDataInterface;
use Amasty\MegaMenuPremium\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuPremium\Model\Menu\Content\Resolver;
use Amasty\MegaMenuPremium\Model\IsNeedMobileContent;
use Magento\Framework\Data\Tree\Node;

class ModifyNodeData implements ModifyNodeDataInterface
{
    /**
     * @var IsNeedMobileContent
     */
    private $isNeedMobileContent;

    /**
     * @var \Amasty\MegaMenuPremium\Model\Menu\Content\Resolver
     */
    private $resolver;

    public function __construct(
        IsNeedMobileContent $isNeedMobileContent,
        Resolver $resolver
    ) {
        $this->isNeedMobileContent = $isNeedMobileContent;
        $this->resolver = $resolver;
    }

    public function execute(Node $node, array $data): array
    {
        $data[ItemInterface::HIDE_MOBILE_CONTENT] = (bool) $node->getData(ItemInterface::HIDE_MOBILE_CONTENT);

        if ($this->isShowMobileContent($node)) {
            $additionalData = [
                ItemInterface::SHOW_MOBILE_CONTENT => (int)$node->getData(ItemInterface::SHOW_MOBILE_CONTENT),
                ItemInterface::MOBILE_CONTENT => $this->resolver->resolve($node)
            ];
            $data = array_merge($additionalData, $data);
        }
        $data[ItemInterface::SUBMENU_ANIMATION] = $node->getData(ItemInterface::SUBMENU_ANIMATION);

        return $data;
    }

    private function isShowMobileContent(Node $node): bool
    {
        return (!$node->getIsCategory() || $this->isNeedMobileContent->execute((int)$node->getLevel()))
            && !$node->getData(ItemInterface::HIDE_MOBILE_CONTENT);
    }
}
