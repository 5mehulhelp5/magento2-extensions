<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\ConfigurableProduct\Block\Product\View\Type\Configurable;

use Amasty\Base\Model\Serializer;
use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class SwatchGalleryReplace
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
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory,
        Serializer $serializer
    ) {
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->serializer = $serializer;
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonConfig(Configurable $subject, string $result): string
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if ($this->lazyConfigProvider->get()->getData('is_lazy')
            || $replaceStrategy === ReplaceStrategies::NONE
        ) {
            return $result;
        }

        $galleryData = $this->serializer->unserialize($result);
        if (empty($galleryData['images'])) {
            return $result;
        }

        foreach ($galleryData['images'] as &$images) {
            foreach ($images as &$imagesSetting) {
                $this->replaceByPatternApplier->execute(ProductGalleryReplace::REPLACE_PATTERN_GROUP, $imagesSetting);
            }
        }

        return $this->serializer->serialize($galleryData);
    }
}
