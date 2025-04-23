<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Label Cache Cleaner for Qty Variable
 */

namespace Amasty\LabelCacheCleanerForQtyVariable\Plugin\Sales\Model\Service\OrderService;

use Amasty\Label\Model\Label;
use Amasty\LabelCacheCleanerForQtyVariable\Model\FlushCache;
use Amasty\LabelCacheCleanerForQtyVariable\Model\GetLabelIdsWithQtyVariable;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderManagementInterface;

class ClearCache
{
    /**
     * @var GetLabelIdsWithQtyVariable
     */
    private $getLabelIdsWithQtyVariable;

    /**
     * @var FlushCache
     */
    private $flushCache;

    public function __construct(GetLabelIdsWithQtyVariable $getLabelIdsWithQtyVariable, FlushCache $flushCache)
    {
        $this->getLabelIdsWithQtyVariable = $getLabelIdsWithQtyVariable;
        $this->flushCache = $flushCache;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(OrderManagementInterface $subject, OrderInterface $order): OrderInterface
    {
        $labelIdsToFlush = $this->getLabelIdsWithQtyVariable->execute(
            $this->getProductIds($order),
            (int)$order->getStoreId()
        );
        if (!empty($labelIdsToFlush)) {
            $this->flushCache->execute(Label::CACHE_TAG, $labelIdsToFlush);
        }

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @return int[]
     */
    private function getProductIds(OrderInterface $order): array
    {
        return array_map(static function (OrderItemInterface $orderItem) {
            return (int)$orderItem->getProductId();
        }, $order->getItems());
    }
}
