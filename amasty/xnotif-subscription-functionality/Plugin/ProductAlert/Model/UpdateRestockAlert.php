<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Plugin\ProductAlert\Model;

use Amasty\XnotifSubscriptionFunctionality\Api\StockAlertRepositoryInterface;
use Amasty\XnotifSubscriptionFunctionality\Model\StockAlertResolver;
use Magento\ProductAlert\Model\Stock as NativeStock;

class UpdateRestockAlert
{
    /**
     * @var StockAlertRepositoryInterface
     */
    private $stockAlertRepository;

    /**
     * @var StockAlertResolver
     */
    private $stockAlertResolver;

    public function __construct(
        StockAlertRepositoryInterface $stockAlertRepository,
        StockAlertResolver $stockAlertResolver
    ) {
        $this->stockAlertRepository = $stockAlertRepository;
        $this->stockAlertResolver = $stockAlertResolver;
    }

    public function beforeAfterSave(NativeStock $subject): void
    {
        if (!$this->stockAlertResolver->execute()) {
            return;
        }

        $this->updateRestockTable($subject);
    }

    private function updateRestockTable(NativeStock $model): void
    {
        if ($stockAlert = $this->stockAlertRepository->getByStockAlertId((int)$model->getAlertStockId())) {
            $stockAlert->setAlertStockId((int)$model->getAlertStockId());
            $stockAlert->setIsRestock(true);
            $this->stockAlertRepository->save($stockAlert);
        }
    }
}
