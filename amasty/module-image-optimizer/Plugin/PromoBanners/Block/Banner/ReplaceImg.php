<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\PromoBanners\Block\Banner;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Amasty\PromoBanners\Block\Banner;

class ReplaceImg
{
    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        ReplaceConfigFactory $replaceConfigFactory,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceByPatternApplier $replaceByPatternApplier
    ) {
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetImageUrl(
        Banner $subject,
        string $result
    ): string {
        $resultArray[] = $result;
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if (!empty($result)
            && !$this->lazyConfigProvider->get()->getData('is_lazy')
            && $replaceStrategy !== ReplaceStrategies::NONE
        ) {
            $this->replaceByPatternApplier->execute(
                ProductGalleryReplace::REPLACE_PATTERN_GROUP,
                $resultArray
            );
        }

        return $resultArray[0];
    }
}
