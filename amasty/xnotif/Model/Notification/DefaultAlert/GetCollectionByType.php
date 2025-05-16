<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert;

use Amasty\Xnotif\Model\Notification\DefaultAlert\Factory\AlertFactoryInterface;
use LogicException;
use Magento\Framework\Data\Collection;

class GetCollectionByType
{
    /**
     * @var AlertFactoryInterface[]
     */
    private $alertFactories;

    public function __construct(array $alertFactories = [])
    {
        $this->initAlertFactories($alertFactories);
    }

    public function execute(string $alertType, ?int $websiteId = null, ?int $storeId = null): Collection
    {
        if (!isset($this->alertFactories[$alertType])) {
            throw new LogicException(sprintf('Alert factory for %s not supported.', $alertType));
        }

        $collection = $this->alertFactories[$alertType]->create();
        if ($websiteId !== null) {
            $collection->addFieldToFilter('website_id', $websiteId);
            $collection->addFieldToFilter('store_id', ['null' => true]);
        } elseif ($storeId !== null) {
            $collection->addFieldToFilter('store_id', $storeId);
        }

        return $collection;
    }

    private function initAlertFactories(array $alertFactories): void
    {
        foreach ($alertFactories as $alertFactory) {
            if (!$alertFactory instanceof AlertFactoryInterface) {
                throw new LogicException(
                    sprintf('Alert factory class must implement %s', AlertFactoryInterface::class)
                );
            }

        }

        $this->alertFactories = $alertFactories;
    }
}
