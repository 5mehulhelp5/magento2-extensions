<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\Resolution\Processors;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\ImageProcessor\ResolutionToolProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ResizeAlgorithm;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Image\Adapter\Gd2;

class Gd2Processor implements ResolutionProcessorInterface
{
    public const TYPE = 'gd2';

    public const POSITION_X = 0;
    public const POSITION_Y = 0;

    /**
     * @var Gd2
     */
    private $gd2;

    /**
     * @var ResolutionToolProvider
     */
    private $resolutionToolProvider;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    public function __construct(
        Gd2 $gd2,
        Filesystem $filesystem,
        ResolutionToolProvider $resolutionToolProvider
    ) {
        $this->gd2 = $gd2;
        $this->resolutionToolProvider = $resolutionToolProvider;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function execute(QueueInterface $queue): void
    {
        $imagePath = $this->mediaDirectory->getAbsolutePath($queue->getFilename());
        try {
            $this->gd2->open($imagePath);
            $this->gd2->keepAspectRatio(true);
            $this->gd2->keepTransparency(true);
        } catch (\Exception $e) {
            return;
        }

        $originalWidth = (int)$this->gd2->getOriginalWidth();
        $originalHeight = (int)$this->gd2->getOriginalHeight();
        if ($originalWidth === 0 || $originalHeight === 0) {
            return;
        }

        $resolutions = $queue->getResolutions();
        foreach (Resolutions::RESOLUTIONS as $resolutionKey => $resolutionData) {
            if ($originalWidth <= $resolutionData['width'] || !in_array($resolutionKey, $resolutions, false)) {
                continue;
            }
            try {
                $this->processResolution($queue, $imagePath, $resolutionData);
            } catch (\Exception $e) {
                continue;
            }
            $this->gd2->open($imagePath);
        }
    }

    private function processResolution(QueueInterface $queue, string $imagePath, array $resolutionData): void
    {
        $width = $resolutionData['width'];
        switch ($queue->getResizeAlgorithm()) {
            case ResizeAlgorithm::RESIZE:
                $this->gd2->resize($width);
                break;
            case ResizeAlgorithm::CROP:
                $this->gd2->crop(self::POSITION_X, self::POSITION_Y, (int)$this->gd2->getOriginalWidth() - $width);
                break;
        }

        $newName = str_replace($queue->getFilename(), $resolutionData['dir'] . $queue->getFilename(), $imagePath);
        $this->prepareWriteDirectory($newName);
        $this->gd2->save($newName);

        foreach ($this->resolutionToolProvider->getTools() as $tool) {
            if ($queue->getData($tool->getToolName())) {
                $tool->process($queue, $newName);
            }
        }
    }

    private function prepareWriteDirectory(string $fileName): void
    {
        $dirName = $this->mediaDirectory->getDriver()->getParentDirectory($fileName);
        if (!$this->mediaDirectory->isExist($dirName)) {
            $this->mediaDirectory->create($dirName);
        }
    }
}
