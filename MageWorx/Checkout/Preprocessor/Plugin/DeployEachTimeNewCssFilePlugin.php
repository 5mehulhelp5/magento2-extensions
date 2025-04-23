<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Preprocessor\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class DeployEachTimeNewCssFilePlugin
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * DeployEachTimeNewCssFilePlugin constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Remove existing static file on each static content deploy
     *
     * @param \Magento\Framework\App\View\Asset\Publisher $subject
     * @param \Magento\Framework\View\Asset\LocalInterface $asset
     * @return \Magento\Framework\View\Asset\LocalInterface[]
     */
    public function beforePublish(
        \Magento\Framework\App\View\Asset\Publisher $subject,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        if ($asset->getModule() === 'MageWorx_Checkout' && $asset->getFilePath() === 'css/main.css') {
            try {
                $dir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
                $dir->delete($asset->getPath());
            } catch (FileSystemException $e) {
                $this->logger->critical($e->getLogMessage());
            }
        }

        return [$asset];
    }
}
