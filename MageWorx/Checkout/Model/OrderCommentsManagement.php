<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Exception;
use MageWorx\Checkout\Api\OrderCommentsManagementInterface;

class OrderCommentsManagement implements OrderCommentsManagementInterface
{
    /**
     * @var \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * @var OrderCommentFactory
     */
    private $orderCommentFactory;

    /**
     * @var ResourceModel\OrderComment
     */
    private $orderCommentResource;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * OrderCommentsManagement constructor.
     *
     * @param \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param \MageWorx\Checkout\Model\OrderCommentFactory $orderCommentFactory
     * @param \MageWorx\Checkout\Model\ResourceModel\OrderComment $orderCommentResource
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        \MageWorx\Checkout\Model\OrderCommentFactory $orderCommentFactory,
        \MageWorx\Checkout\Model\ResourceModel\OrderComment $orderCommentResource,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->orderCommentFactory    = $orderCommentFactory;
        $this->orderCommentResource   = $orderCommentResource;
        $this->logger                 = $logger;
    }

    /**
     * @inheritDoc
     */
    public function saveCommentGuest(string $comment, string $cartId): bool
    {
        $quoteId = $this->getQuoteIdByMask($cartId);
        if (!$quoteId) {
            return false;
        }

        return $this->saveComment($comment, $quoteId);
    }

    /**
     * @inheritDoc
     */
    public function saveComment(string $comment, int $cartId): bool
    {
        /** @var \MageWorx\Checkout\Model\OrderComment $model */
        $model = $this->orderCommentFactory->create();
        $this->orderCommentResource->load($model, $cartId, 'quote_id');
        if ($model->getId()) {
            $model->setData('comment', $comment);
        } else {
            $model->addData(
                [
                    'quote_id' => $cartId,
                    'comment'  => $comment
                ]
            );
        }

        try {
            $this->orderCommentResource->save($model);
        } catch (Exception $e) {
            $this->logger->alert($e);

            return false;
        }

        return true;
    }

    /**
     * Get comment by quote id
     *
     * @param int $quoteId
     * @return string
     */
    public function getOrderCommentByQuoteId(int $quoteId): string
    {
        /** @var \MageWorx\Checkout\Model\OrderComment $model */
        $model = $this->orderCommentFactory->create();
        $this->orderCommentResource->load($model, $quoteId, 'quote_id');
        $comment = $model->getData('comment') ?? '';

        return $comment;
    }

    /**
     * @param string $mask
     * @return int|null
     */
    private function getQuoteIdByMask(string $mask): ?int
    {
        try {
            $id = $this->maskedQuoteIdToQuoteId->execute($mask);
        } catch (Exception $exception) {
            $this->logger->alert($exception);

            return null;
        }

        return $id;
    }
}
