<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Model\GroupAttr\Query;

use Amasty\GroupedOptions\Api\Data\GroupAttrInterface;
use Amasty\GroupedOptions\Model\ResourceModel\GroupAttr\Collection;
use Amasty\GroupedOptions\Model\ResourceModel\GroupAttr\CollectionFactory;

class IsGroupCodeExist
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(string $code): bool
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        return (bool) $collection->addFieldToFilter(GroupAttrInterface::GROUP_CODE, $code)->getSize();
    }
}
