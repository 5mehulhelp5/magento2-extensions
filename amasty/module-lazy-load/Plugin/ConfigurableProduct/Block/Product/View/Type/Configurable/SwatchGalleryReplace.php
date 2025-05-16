<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Plugin\ConfigurableProduct\Block\Product\View\Type\Configurable;

use Amasty\Base\Model\Serializer;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\LazyLoad\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;

class SwatchGalleryReplace
{
    /**
     * @var LazyConfig
     */
    private $lazyConfig;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        LazyConfigFactory $lazyConfigFactory,
        ReplaceByPatternApplier $replaceByPatternApplier,
        Serializer $serializer
    ) {
        $this->lazyConfig = $lazyConfigFactory->create();
        $this->serializer = $serializer;
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonConfig(Configurable $subject, string $result): string
    {
        if (!$this->lazyConfig->getData(LazyConfig::IS_LAZY)) {
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
