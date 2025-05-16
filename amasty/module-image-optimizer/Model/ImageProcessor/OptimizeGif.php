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
use Magento\Framework\Exception\LocalizedException;

class OptimizeGif implements ImageProcessorInterface
{
    /**
     * @var CommandProvider
     */
    private $gifCommandProvider;

    /**
     * @var array
     */
    private $availableTools = [];

    public function __construct(CommandProvider $gifCommandProvider)
    {
        $this->gifCommandProvider = $gifCommandProvider;
    }

    public function process(QueueInterface $queue): void
    {
        if (!$queue->getTool()) {
            return;
        }

        $gifCommand = $this->gifCommandProvider->get($queue->getTool());
        $filename = (string)$queue->getFilename();

        $gifCommand->run(
            $queue,
            $filename,
            $filename
        );
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        if (!$imageSetting->getGifTool()) {
            return false;
        }
        if (!isset($this->availableTools[$imageSetting->getGifTool()])) {
            try {
                $this->availableTools[$imageSetting->getGifTool()] = $this->gifCommandProvider
                    ->get($imageSetting->getGifTool())
                    ->isAvailable();
            } catch (LocalizedException $e) {
                $this->availableTools[$imageSetting->getGifTool()] = false;
            }
        }

        if (!$this->availableTools[$imageSetting->getGifTool()]) {
            $queue->setTool('');

            return false;
        }
        $queue->setTool($imageSetting->getGifTool());

        return true;
    }
}
