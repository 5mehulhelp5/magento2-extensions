<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Plugin\View\Template\Html;

use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\Js\InlineJsMinifier;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Template\Html\Minifier;

class MinifierPlugin
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var InlineJsMinifier
     */
    private $inlineJsMinifier;

    public function __construct(
        Filesystem $filesystem,
        ConfigProvider $configProvider,
        InlineJsMinifier $inlineJsMinifier
    ) {
        $this->filesystem = $filesystem;
        $this->configProvider = $configProvider;
        $this->inlineJsMinifier = $inlineJsMinifier;
    }

    public function afterMinify(Minifier $subject, $minificationResult, $file)
    {
        if (!$this->configProvider->isMifiniedJs() || !$this->configProvider->isMinifiedJsInPhtml()) {
            return $minificationResult;
        }

        $htmlDirectoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::TMP_MATERIALIZATION_DIR);
        $fileRelativePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getRelativePath($file);
        if (in_array($fileRelativePath, $this->configProvider->getMinifyJsPhtmlBlacklist())) {
            return $minificationResult;
        }

        $minifiedFileContent = preg_replace_callback(
            '/(?<!<<)<script[^>]*>(?>.*?<\/script>)/is',
            function ($scriptContent) {
                return $this->inlineJsMinifier->minify($scriptContent[0]);
            },
            $htmlDirectoryWrite->readFile($fileRelativePath)
        );

        return $htmlDirectoryWrite->writeFile($fileRelativePath, rtrim($minifiedFileContent));
    }
}
