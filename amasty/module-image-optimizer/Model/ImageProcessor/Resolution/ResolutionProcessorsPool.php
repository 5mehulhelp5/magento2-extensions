<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\Resolution;

use Amasty\ImageOptimizer\Model\ImageProcessor\Resolution\Processors\ResolutionProcessorInterface;

class ResolutionProcessorsPool
{
    /**
     * @var ResolutionProcessorInterface[]
     */
    private $processors;

    /**
     * @var ResolutionProcessorInterface
     */
    private $defaultProcessor;

    public function __construct(
        Processors\Gd2Processor $defaultProcessor,
        array $processors = []
    ) {
        $this->initProcessors($processors);
        $this->defaultProcessor = $defaultProcessor;
    }

    public function getByType(string $type): ResolutionProcessorInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->getType() === $type) {
                return $processor;
            }
        }

        return $this->defaultProcessor;
    }

    /**
     * @param ResolutionProcessorInterface[] $processors
     * @return void
     * @throws \LogicException
     */
    private function initProcessors(array $processors): void
    {
        foreach ($processors as $type => $processor) {
            if (!$processor instanceof ResolutionProcessorInterface) {
                throw new \LogicException(
                    sprintf('Resolution Processor must implement %s', ResolutionProcessorInterface::class)
                );
            }
            $this->processors[$type] = $processor;
        }
    }
}
