<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\Processor;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Menu\Item;
use Amasty\MegaMenuLite\Model\Repository\ItemRepository;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\GetMaxSortOrder;
use Magento\Store\Model\Store;

class SaveItem
{
    /**
     * @var GetMaxSortOrder
     */
    private $maxSortOrder;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    public function __construct(
        GetMaxSortOrder $maxSortOrder,
        ItemRepository $itemRepository
    ) {
        $this->maxSortOrder = $maxSortOrder;
        $this->itemRepository = $itemRepository;
    }

    public function execute(int $linkEntityId, array $inputData): void
    {
        $itemContent = $this->getItemContent($inputData);
        $inputData[ItemInterface::ENTITY_ID] = $linkEntityId;
        $itemContent->addData($inputData);
        $this->itemRepository->save($itemContent);
    }

    private function getItemContent(array $data): ItemInterface
    {
        if (!empty($data[Item::ENTITY_ID])) {
            if ($storeId = (int) ($data['store_id'] ?? Store::DEFAULT_STORE_ID)) {
                $itemContentTemp = $this->itemRepository->getByEntityId(
                    $data[Item::ENTITY_ID],
                    $storeId,
                    Item::CUSTOM_TYPE
                );
                $itemContent = $itemContentTemp ?: $this->itemRepository->getNew();
                $itemContent->setStoreId($storeId);
            } else {
                $itemContent = $this->itemRepository->getByEntityId(
                    $data[Item::ENTITY_ID],
                    Store::DEFAULT_STORE_ID,
                    Item::CUSTOM_TYPE
                );
            }
        } else {
            $itemContent = $this->itemRepository->getNew();
        }
        $itemContent->setType(Item::CUSTOM_TYPE);

        return $itemContent;
    }
}
