<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\DataProvider\DataCollector;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\ItemRepositoryInterface;
use Amasty\MegaMenuLite\Model\Backend\DataProvider\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;

class General implements DataCollectorInterface
{
    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var FieldsByStore
     */
    private $fieldsByStore;

    public function __construct(
        ItemRepositoryInterface $itemRepository,
        FieldsByStore $fieldsByStore
    ) {
        $this->itemRepository = $itemRepository;
        $this->fieldsByStore = $fieldsByStore;
    }

    public function execute(array $data, int $storeId, int $entityId): array
    {
        if ($storeId) {
            $data['store_id'] = $storeId;
            /** @var ItemInterface $item */
            $item = $this->itemRepository->getByEntityId($entityId, $storeId, 'custom');
            if ($item) {
                $data = $this->collectItemData($item, $data);
            }
        }

        return $data;
    }

    private function collectItemData(ItemInterface $item, array $data): array
    {
        foreach ($this->fieldsByStore->getCustomFields() as $fieldSet) {
            foreach ($fieldSet as $field) {
                if ($item->getData($field) !== null) {
                    $data[$field] = $item->getData($field);
                }
            }
        }

        return $data;
    }
}
