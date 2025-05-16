<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Command\Utils;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\ObjectManagerInterface;
use Magento\RemoteStorage\Driver\DriverPool as RemoteDriverPool;
use Magento\RemoteStorage\Model\Config;

class RemoteStorageProcessor
{
    /**
     * @var WriteInterface
     */
    private $remoteMedia;

    /**
     * @var WriteInterface
     */
    private $localMedia;

    /**
     * @var bool
     */
    private $isEnabled = false;

    public function __construct(
        Filesystem $filesystem,
        ObjectManagerInterface $objectManager
    ) {
        if (class_exists(Config::class)) {
            $remoteStorageConfig = $objectManager->get(Config::class);
            $this->isEnabled = $remoteStorageConfig->isEnabled();
            $this->remoteMedia = $filesystem->getDirectoryWrite(DirectoryList::MEDIA, RemoteDriverPool::REMOTE);
            $this->localMedia = $filesystem->getDirectoryWrite(DirectoryList::MEDIA, DriverPool::FILE);
        }
    }

    public function copyFromRemote(string $filepath): void
    {
        if ($this->isEnabled) {
            $filepath = str_replace($this->remoteMedia->getAbsolutePath(), '', $filepath);
            $this->copyFile($filepath, $this->remoteMedia, $this->localMedia);
        }
    }

    public function copyToRemote(string $filepath): void
    {
        if ($this->isEnabled) {
            $filepath = str_replace($this->localMedia->getAbsolutePath(), '', $filepath);
            $this->copyFile($filepath, $this->localMedia, $this->remoteMedia);
        }
    }

    public function isRemoteStorageEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param string $filepath
     * @param WriteInterface $from
     * @param WriteInterface $to
     *
     * @return void
     * @throws LocalizedException
     */
    private function copyFile(string $filepath, WriteInterface $from, WriteInterface $to): void
    {
        if ($from->isExist($filepath)) {
            $file = $to->openFile($filepath, 'w');
            try {
                $file->lock();
                $file->write($from->readFile($filepath));
                $file->unlock();
                $file->close();
            } catch (FileSystemException $e) {
                $file->close();
                throw new LocalizedException(
                    __('Unable to copy file %1 for optimization processing.', $filepath)
                );
            }
        }
    }
}
