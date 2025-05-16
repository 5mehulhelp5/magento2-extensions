<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class DumpOriginal implements ImageProcessorInterface
{
    public const DUMP_DIRECTORY = 'amasty' . DIRECTORY_SEPARATOR . 'amoptimizer_dump' . DIRECTORY_SEPARATOR;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(Filesystem $filesystem)
    {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->isDumpOriginal()) {
            return;
        }
        $file = $queue->getFilename();
        $dumpFile = self::DUMP_DIRECTORY . $file;
        if ($this->isAvailableCopy($file, $dumpFile)) {
            $this->mediaDirectory->copyFile($file, $dumpFile);
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        $queue->setIsDumpOriginal($imageSetting->isDumpOriginal());

        return $queue->isDumpOriginal();
    }

    private function isAvailableCopy(string $file, string $dumpFile): bool
    {
        return !$this->mediaDirectory->isExist($dumpFile)
            || ($this->mediaDirectory->isExist($dumpFile)
                && $this->mediaDirectory->stat($dumpFile)['size'] !== $this->mediaDirectory->stat($file)['size']
            );
    }
}
