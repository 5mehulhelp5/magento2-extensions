<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Plugin;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use MageWorx\Checkout\Api\OrderCommentsManagementInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SaveOrderComment
 *
 * Saving customer comment for the order after placing an order in the general order history
 */
class SaveOrderComment
{
    /**
     * @var OrderCommentsManagementInterface
     */
    private $commentsManagement;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SaveOrderComment constructor.
     *
     * @param OrderCommentsManagementInterface $commentsManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderCommentsManagementInterface $commentsManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->commentsManagement = $commentsManagement;
        $this->orderRepository    = $orderRepository;
        $this->logger             = $logger;
    }

    /**
     * @param \Magento\Quote\Api\CartManagementInterface $subject
     * @param int $result
     * @param int $cartId
     * @param PaymentInterface|null $paymentMethod
     * @return int
     */
    public function afterPlaceOrder(
        \Magento\Quote\Api\CartManagementInterface $subject,
        $result,
        $cartId,
        PaymentInterface $paymentMethod = null
    ): int {
        if (!$result) {
            return $result;
        }

        try {
            $comment = $this->commentsManagement->getOrderCommentByQuoteId($cartId);
            if (!$comment) {
                return $result;
            }

            /** @var \Magento\Sales\Model\Order $history */
            $order = $this->orderRepository->get($result);
            $order->addCommentToStatusHistory($comment, false, true);
            $this->orderRepository->save($order);
        } catch (\Exception $exception) {
            $this->logger->alert($exception);
        }

        return $result;
    }
}
