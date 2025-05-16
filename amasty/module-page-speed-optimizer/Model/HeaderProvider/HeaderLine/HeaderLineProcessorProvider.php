<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\HeaderProvider\HeaderLine;

use Amasty\PageSpeedOptimizer\Model\HeaderProvider\HeaderLine\Processor\HeaderLineInterface;

class HeaderLineProcessorProvider
{
    /**
     * @var array ['key' => HeaderLineInterface, ...]
     */
    private $processors = [];

    /**
     * @param array $headerLineProcessors
     * [ ['processor_code' => ['key' => int, 'processor' => HeaderLineInterface]], ... ]
     */
    public function __construct(
        array $headerLineProcessors = []
    ) {
        $this->initializeProcessors($headerLineProcessors);
    }

    public function getProcessorByType(int $type): ?HeaderLineInterface
    {
        return $this->processors[$type] ?? null;
    }

    private function initializeProcessors(array $processors): void
    {
        foreach ($processors as $processor) {
            if (!$processor['processor'] instanceof HeaderLineInterface) {
                throw new \LogicException(
                    sprintf('HeaderLine Processor type must implement %s', HeaderLineInterface::class)
                );
            }
            $this->processors[$processor['key']] = $processor['processor'];
        }
    }
}
