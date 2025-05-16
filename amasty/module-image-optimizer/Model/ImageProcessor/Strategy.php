<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

class Strategy
{
    /**
     * @var array
     */
    private $extensions;

    /**
     * @var array
     */
    private $strategy;

    public function __construct(
        array $extensions = [],
        array $strategy = []
    ) {
        $this->extensions = $extensions;
        $this->strategy = $strategy;
    }

    public function getStrategy(): array
    {
        return $this->strategy;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
