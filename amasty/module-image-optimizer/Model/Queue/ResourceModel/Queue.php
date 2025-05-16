<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Queue\ResourceModel;

use Amasty\ImageOptimizer\Model\Queue\Queue as Model;
use Amasty\ImageOptimizer\Model\Queue\Queue as QueueModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Queue extends AbstractDb
{
    public const TABLE_NAME = 'amasty_page_speed_optimizer_queue';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, QueueModel::QUEUE_ID);
    }

    /**
     * @return array ['first_filename', 'second_filename', ...]
     */
    public function getFilenames(): array
    {
        $select = $this->getConnection()->select()->from($this->getMainTable(), [Model::FILENAME]);

        return $this->getConnection()->fetchCol($select);
    }

    public function clear(array $queueTypes): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::QUEUE_TYPE . ' IN (?)' => $queueTypes]);
    }

    public function deleteByIds(array $ids = []): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::QUEUE_ID . ' in (?) ' => $ids]);
    }

    public function deleteByFilename(string $filename): void
    {
        $this->getConnection()->delete($this->getMainTable(), [QueueModel::FILENAME . ' = ?' => $filename]);
    }

    public function deleteByFilenamesAndType(array $filenames, string $queueType): void
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            [
                QueueModel::FILENAME . ' IN (?)' => $filenames,
                QueueModel::QUEUE_TYPE . ' = ?' => $queueType,
            ]
        );
    }
}
