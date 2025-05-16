<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\Repository;

use Amasty\XnotifSubscriptionFunctionality\Api\Data\StockAlertInterface;
use Amasty\XnotifSubscriptionFunctionality\Api\StockAlertRepositoryInterface;
use Amasty\XnotifSubscriptionFunctionality\Model\StockAlertFactory;
use Amasty\XnotifSubscriptionFunctionality\Model\ResourceModel\StockAlert as StockAlertResource;
use Magento\Framework\Exception\CouldNotSaveException;

class StockAlertRepository implements StockAlertRepositoryInterface
{
    /**
     * @var StockAlertFactory
     */
    private $stockAlertFactory;

    /**
     * @var StockAlertResource
     */
    private $stockAlertResource;

    /**
     *
     * @var StockAlertInterface[]
     */
    private $stockAlerts;

    public function __construct(
        StockAlertFactory $stockAlertFactory,
        StockAlertResource $stockAlertResource
    ) {
        $this->stockAlertFactory = $stockAlertFactory;
        $this->stockAlertResource = $stockAlertResource;
    }

    public function save(StockAlertInterface $stockAlert): StockAlertInterface
    {
        try {
            $this->stockAlertResource->save($stockAlert);
            unset($this->stockAlerts[$stockAlert->getId()]);
        } catch (\Exception $e) {
            if ($stockAlert->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save profile with ID %1. Error: %2',
                        [$stockAlert->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new profile. Error: %1', $e->getMessage()));
        }

        return $stockAlert;
    }

    public function getByStockAlertId(int $id): StockAlertInterface
    {
        if (!isset($this->profiles[$id])) {
            $stockAlert = $this->stockAlertFactory->create();
            $this->stockAlertResource->load($stockAlert, $id, StockAlertInterface::ALERT_STOCK_ID);
            $this->stockAlerts[$id] = $stockAlert;
        }

        return $this->stockAlerts[$id];
    }
}
