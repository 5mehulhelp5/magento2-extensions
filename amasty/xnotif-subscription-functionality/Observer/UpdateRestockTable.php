<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Observer;

use Amasty\XnotifSubscriptionFunctionality\Api\StockAlertRepositoryInterface;
use Amasty\XnotifSubscriptionFunctionality\Model\StockAlertResolver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\ProductAlert\Model\Stock;

/**
 * amasty_xnotif_after_save_stock_model
 */
class UpdateRestockTable implements ObserverInterface
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

    public function execute(Observer $observer): void
    {
        if (!$this->stockAlertResolver->execute()) {
            return;
        }

        /** @var Stock $model */
        $model = $observer->getModel();
        if ($stockAlert = $this->stockAlertRepository->getByStockAlertId((int)$model->getAlertStockId())) {
            $stockAlert->setAlertStockId((int)$model->getAlertStockId());
            $stockAlert->setIsRestock(true);
            $this->stockAlertRepository->save($stockAlert);
        }
    }
}
