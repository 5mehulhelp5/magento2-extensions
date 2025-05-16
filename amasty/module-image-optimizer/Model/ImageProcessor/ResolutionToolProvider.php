<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Model\ImageProcessor\ResolutionToolProcessor\ToolInterface;

class ResolutionToolProvider
{
    /**
     * @var ToolInterface[]
     */
    private $tools;

    /**
     * @param array $toolTypes
     * [ 'tool_code' => Amasty\ImageOptimizer\Model\ImageProcessor\ResolutionToolProcessor\ToolInterface, ... ]
     */
    public function __construct(
        array $toolTypes = []
    ) {
        $this->initializeToolTypes($toolTypes);
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    private function initializeToolTypes(array $toolTypes): void
    {
        foreach ($toolTypes as $type => $tool) {
            if (!$tool instanceof ToolInterface) {
                throw new \LogicException(
                    sprintf('Tool type must implement %s', ToolInterface::class)
                );
            }
            $this->tools[$type] = $tool;
        }
    }
}
