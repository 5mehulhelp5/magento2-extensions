<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Image\ReplaceAlgorithm\Checker;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\Checker\AlgorithmCheckerInterface;

class ReplacePictureChecker implements AlgorithmCheckerInterface
{
    /**
     * @var LazyConfigFactory
     */
    private $lazyConfigFactory;

    public function __construct(
        LazyConfigFactory $lazyConfigFactory
    ) {
        $this->lazyConfigFactory = $lazyConfigFactory;
    }

    public function check(): bool
    {
        return !$this->lazyConfigFactory->create()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT);
    }
}
