<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Frontend;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Menu\Content\Resolver;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\Data\Tree\Node;

class ModifyNodeData implements ModifyNodeDataInterface
{
    /**
     * @var Resolver
     */
    private $contentResolver;

    public function __construct(
        Resolver $contentResolver
    ) {
        $this->contentResolver = $contentResolver;
    }

    public function execute(Node $node, array $data): array
    {
        $additionalData = [
            ItemInterface::NAME => $node->getData('name'),
            GetItemData::IS_CATEGORY => $node->getData(GetItemData::IS_CATEGORY),
            ItemInterface::ID => $node->getData(ItemInterface::ID),
            ItemInterface::STATUS => $this->getNodeStatus($node),
            Position::POSITION => $node->getData(Position::POSITION),
            'content' => $this->contentResolver->resolve($node),
            'url' => $node->getData('url'),
            'current' => $node->getData(GetItemData::HAS_ACTIVE) || $node->getData(GetItemData::IS_ACTIVE)
        ];

        if ($node->getData(ItemInterface::LABEL)) {
            $additionalData[ItemInterface::LABEL] = [
                ItemInterface::LABEL => $node->getData(ItemInterface::LABEL),
                ItemInterface::LABEL_TEXT_COLOR => $node->getData(ItemInterface::LABEL_TEXT_COLOR),
                ItemInterface::LABEL_BACKGROUND_COLOR => $node->getData(ItemInterface::LABEL_BACKGROUND_COLOR)
            ];
        }

        return array_merge($data, $additionalData);
    }

    private function getNodeStatus(Node $node): int
    {
        return $node->getData(GetItemData::IS_CATEGORY) ? 1 : (int) $node->getData(ItemInterface::STATUS);
    }
}
