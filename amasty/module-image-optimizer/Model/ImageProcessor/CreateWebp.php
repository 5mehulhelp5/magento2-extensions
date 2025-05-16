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
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\ImageOptimizer\Model\Command\GifCwebp;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\DriverPool;

class CreateWebp implements ImageProcessorInterface
{
    /**
     * @var CommandProvider
     */
    private $webpCommandProvider;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var array
     */
    private $availableTools = [];

    public function __construct(
        CommandProvider $webpCommandProvider,
        Filesystem $filesystem
    ) {
        $this->webpCommandProvider = $webpCommandProvider;
        $this->filesystem = $filesystem;
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->getWebpTool()) {
            return;
        }

        $webpCommand = $this->webpCommandProvider->get($queue->getWebpTool());
        $filename = (string)$queue->getFilename();

        $webpCommand->run(
            $queue,
            $filename,
            $this->getWebpFileName($filename, $queue)
        );
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        $webpTool = $imageSetting->getWebpTool();
        if (!$webpTool) {
            return false;
        }
        if ($queue->getExtension() === 'gif') {
            $webpTool = GifCwebp::GIFCWEBP_TYPE;
        }

        if (!isset($this->availableTools[$webpTool])) {
            try {
                $this->availableTools[$webpTool] = $this->webpCommandProvider
                    ->get($webpTool)
                    ->isAvailable();
            } catch (LocalizedException $e) {
                $this->availableTools[$webpTool] = false;
            }
        }

        if (!$this->availableTools[$webpTool]) {
            $queue->setWebpTool('');

            return false;
        }
        $queue->setWebpTool($webpTool);

        return true;
    }

    private function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA, DriverPool::FILE);
        }

        return $this->mediaDirectory;
    }

    private function getWebpFileName(string $imagePath, QueueInterface $queue): string
    {
        $webPPath = str_replace(
            $queue->getFilename(),
            Resolutions::WEBP_DIR . $queue->getFilename(),
            $this->getMediaDirectory()->getAbsolutePath($imagePath)
        );
        if (!$this->getMediaDirectory()->isExist($this->dirname($webPPath))) {
            $this->getMediaDirectory()->create($this->dirname($webPPath));
        }

        return str_replace(
            '.' . $queue->getExtension(),
            '_' . $queue->getExtension() . '.webp',
            $webPPath
        );
    }

    private function dirname(string $file): string
    {
        //phpcs:ignore
        return dirname($file);
    }
}
