<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\ResolutionToolProcessor;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\ImageOptimizer\Model\Queue\Queue;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Webp implements ToolInterface
{
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var CommandProvider
     */
    private $commandProvider;

    public function __construct(
        Filesystem $filesystem,
        CommandProvider $commandProvider
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->commandProvider = $commandProvider;
    }

    public function process(QueueInterface $queue, string $newName): void
    {
        $newQueue = clone $queue;
        $newQueue->setFilename($this->mediaDirectory->getRelativePath($newName));
        $webpCommand = $this->commandProvider->get($newQueue->getWebpTool());
        $filename = (string)$newQueue->getFilename();
        $webP = str_replace(
            '.' .  $newQueue->getExtension(),
            '_'. $newQueue->getExtension() . '.webp',
            $filename
        );

        $webpCommand->run($newQueue, $filename, $webP);
    }

    public function getToolName(): string
    {
        return Queue::WEBP_TOOL;
    }
}
