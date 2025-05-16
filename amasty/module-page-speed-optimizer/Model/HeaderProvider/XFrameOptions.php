<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\HeaderProvider;

use Magento\Framework\App\Response\HeaderProvider\AbstractHeaderProvider;

class XFrameOptions extends AbstractHeaderProvider
{
    /**
     * @var IsSetXFrameOptions
     */
    private $isSetXFrameOptions;

    public function __construct(
        IsSetXFrameOptions $isSetXFrameOptions
    ) {
        $this->isSetXFrameOptions = $isSetXFrameOptions;
    }

    public function canApply(): bool
    {
        return $this->isSetXFrameOptions->isSetHeader();
    }

    public function getName(): string
    {
        return 'x-frame-options';
    }

    public function getValue(): string
    {
        return 'allow-from ' . $this->isSetXFrameOptions->getBaseUrl();
    }
}
