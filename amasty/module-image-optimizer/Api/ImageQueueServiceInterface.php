<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Api;

interface ImageQueueServiceInterface
{
    /**
     * @param \Amasty\ImageOptimizer\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function addToQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void;

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\QueueInterface $queue
     *
     * @return void
     */
    public function removeFromQueue(\Amasty\ImageOptimizer\Api\Data\QueueInterface $queue): void;

    /**
     * @param string $filename
     *
     * @return void
     */
    public function deleteByFilename(string $filename): void;

    /**
     * @param int $limit
     * @param array $queueTypes
     *
     * @return \Amasty\ImageOptimizer\Api\Data\QueueInterface[]
     */
    public function shuffleQueues(int $limit = 10, array $queueTypes = []): array;

    /**
     * @param array $queueTypes
     * @return void
     */
    public function clearQueue(array $queueTypes): void;

    /**
     * @return bool
     */
    public function isQueueEmpty(): bool;

    /**
     * @param string|null $type
     * @return int
     */
    public function getQueueSize(?string $type = null): int;
}
