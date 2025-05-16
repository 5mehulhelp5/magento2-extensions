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

class RestoreFromOriginal implements ImageProcessorInterface
{
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
        $file = $queue->getFilename();
        $dumpFile = DumpOriginal::DUMP_DIRECTORY . $file;
        if ($this->isAvailableCopy($file, $dumpFile)) {
            $this->mediaDirectory->copyFile($dumpFile, $file);
        }
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        return false;
    }

    private function isAvailableCopy(string $file, string $dumpFile): bool
    {
        $isAvailable = false;
        if ($this->mediaDirectory->isExist($dumpFile)) {
            $isAvailable = true;
            // To prevent copying different dump file with the same name to original directory we compare size of files.
            if ($this->mediaDirectory->isExist($file)
                && $this->mediaDirectory->stat($dumpFile)['size'] !== $this->mediaDirectory->stat($file)['size']) {
                $isAvailable = false;
            }
        }

        return $isAvailable;
    }
}
