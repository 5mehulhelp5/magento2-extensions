<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Queue;

use Amasty\ImageOptimizer\Api\ImageQueueServiceInterface;

class ImageQueueService implements ImageQueueServiceInterface
{
    /**
     * @var ResourceModel\Queue
     */
    private $queueResource;

    /**
     * @var ResourceModel\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\ImageOptimizer\Model\Queue\ResourceModel\Queue $queueResource,
        \Amasty\ImageOptimizer\Model\Queue\ResourceModel\CollectionFactory $collectionFactory
    ) {
        $this->queueResource = $queueResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function addToQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void
    {
        $this->queueResource->save($queue);
    }

    /**
     * @return array ['first_filename', 'second_filename', ...]
     */
    public function getFilenames(): array
    {
        return $this->queueResource->getFilenames();
    }

    public function removeFromQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void
    {
        try {
            $this->queueResource->delete($queue);
        } catch (\Exception $e) {
            null;
        }
    }

    public function deleteByFilename(string $filename): void
    {
        $this->queueResource->deleteByFilename($filename);
    }

    public function deleteByFilenamesAndType(array $filenames, string $queueType): void
    {
        $this->queueResource->deleteByFilenamesAndType($filenames, $queueType);
    }

    public function clearQueue(array $queueTypes): void
    {
        $this->queueResource->clear($queueTypes);
    }

    public function shuffleQueues(int $limit = 10, array $queueTypes = []): array
    {
        /** @var ResourceModel\Collection $queueCollection */
        $queueCollection = $this->collectionFactory->create();
        if ($queueTypes) {
            $queueCollection->addFieldToFilter(Queue::QUEUE_TYPE, ['in' => $queueTypes]);
        }
        $queueCollection->setPageSize((int)$limit);

        $items = $queueCollection->getItems();
        /** @var \Amasty\ImageOptimizer\Api\Data\QueueInterface $queue */
        $ids = [];
        foreach ($items as $queue) {
            $ids[] = $queue->getQueueId();
        }
        if (!empty($ids)) {
            $this->queueResource->deleteByIds($ids);
        }

        return $items;
    }

    public function isQueueEmpty(): bool
    {
        return !(bool)$this->collectionFactory->create()->getSize();
    }

    public function getQueueSize(?string $type = null): int
    {
        $collection = $this->collectionFactory->create();
        if ($type) {
            $collection->addFieldToFilter(Queue::QUEUE_TYPE, $type);
        }

        return $collection->getSize();
    }
}
