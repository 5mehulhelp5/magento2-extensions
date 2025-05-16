<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image\ReplaceAlgorithm\Checker;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\Checker\AlgorithmCheckerInterface;

class ReplaceBestChecker implements AlgorithmCheckerInterface
{
    /**
     * @var ReplaceConfigFactory
     */
    private $replaceConfigFactory;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    public function __construct(
        ReplaceConfigFactory $replaceConfigFactory,
        LazyConfigProvider $lazyConfigProvider
    ) {
        $this->replaceConfigFactory = $replaceConfigFactory;
        $this->lazyConfigProvider = $lazyConfigProvider;
    }

    public function check(): bool
    {
        $replaceConfig = $this->replaceConfigFactory->create();

        return $replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY) === ReplaceStrategies::SMART_OPTIMIZATION
            && $replaceConfig->getData(ReplaceConfig::IS_REPLACE_WITH_USER_AGENT)
            && !$this->lazyConfigProvider->get()->getData('is_lazy');
    }
}
