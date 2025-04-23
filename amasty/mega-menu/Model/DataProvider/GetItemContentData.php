<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\DataProvider;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\ItemRepositoryInterface;
use Magento\Store\Model\Store;

class GetItemContentData
{
    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    public function __construct(
        ItemRepositoryInterface $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param string $field
     * @param int $entityId
     * @param int|null $storeId
     * @param string|null $type
     * @return array|mixed|null
     */
    public function execute(
        string $field,
        int $entityId,
        ?int $storeId = Store::DEFAULT_STORE_ID,
        ?string $type = ItemInterface::CATEGORY_TYPE
    ) {
        /** @var ItemInterface $itemContent */
        $itemContent = $this->itemRepository->getByEntityId($entityId, Store::DEFAULT_STORE_ID, $type);
        $value = $itemContent ? $itemContent->getData($field) : null;

        $itemContent = $this->itemRepository->getByEntityId($entityId, $storeId, $type);
        if ($itemContent && $itemContent->getData($field) !== null) {
            $value = $itemContent->getData($field);
        }

        return $value;
    }
}
