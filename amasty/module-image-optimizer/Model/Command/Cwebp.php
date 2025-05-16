<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Command;

use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\ConfigProvider;
use Magento\Framework\Filesystem;
use Magento\Framework\Shell;
use Psr\Log\LoggerInterface;

class Cwebp extends ShellCommand
{
    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    public function __construct(
        Shell $shell,
        Filesystem $filesystem,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        parent::__construct($shell, $filesystem, $logger);
        $this->configProvider = $configProvider;
    }

    public function getName(): string
    {
        return (string)__('Cwebp');
    }

    public function getType(): string
    {
        return 'cwebp';
    }

    protected function getCommand(): string
    {
        return 'cwebp -q ' . $this->configProvider->getWebpCompressionQuality() . ' %s -o %s';
    }

    protected function getCheckCommand(): ?string
    {
        return 'cwebp -help';
    }

    protected function getCheckResult(): ?string
    {
        return 'cwebp [options]';
    }

    protected function prepareArguments(QueueInterface $queue, string $inputFile = '', string $outputFile = ''): array
    {
        return [
            $this->getMediaDirectory()->getAbsolutePath($inputFile),
            $this->getMediaDirectory()->getAbsolutePath($outputFile)
        ];
    }
}
