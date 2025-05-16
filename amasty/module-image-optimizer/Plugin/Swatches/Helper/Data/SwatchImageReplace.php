<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Swatches\Helper\Data;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Swatches\Helper\Data;

class SwatchImageReplace
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
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductMediaGallery(Data $helper, array $result): array
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if (!empty($result)
            && !$this->lazyConfigProvider->get()->getData('is_lazy')
            && $replaceStrategy !== ReplaceStrategies::NONE
        ) {
            $this->replaceByPatternApplier->execute(ProductGalleryReplace::REPLACE_PATTERN_GROUP, $result);
            if (!empty($result['gallery'])) {
                foreach ($result['gallery'] as &$images) {
                    $this->replaceByPatternApplier->execute(
                        ProductGalleryReplace::REPLACE_PATTERN_GROUP,
                        $images
                    );
                }
            }
        }

        return $result;
    }
}
