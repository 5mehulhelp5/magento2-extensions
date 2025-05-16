<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Swatches\Block\Product\Renderer\Configurable;

use Amasty\Base\Model\Serializer;
use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Swatches\Block\Product\Renderer\Configurable;

class CategoryPageSwatchImageReplace
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    public function __construct(
        Serializer $serializer,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceByPatternApplier $replaceByPatternApplier,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->serializer = $serializer;
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceByPatternApplier = $replaceByPatternApplier;
        $this->replaceConfig = $replaceConfigFactory->create();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsonSwatchConfig(Configurable $subject, string $result): string
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if ($this->lazyConfigProvider->get()->getData('is_lazy')
            || $replaceStrategy === ReplaceStrategies::NONE
        ) {
            return $result;
        }

        $attributes = $this->serializer->unserialize($result);
        foreach ($attributes as &$attribute) {
            if (!isset($attribute['additional_data'])) {
                continue;
            }

            $additionalData = $this->serializer->unserialize($attribute['additional_data']);
            if (!empty($additionalData['use_product_image_for_swatch'])) {
                $this->processAttributeImage($attribute);

                return $this->serializer->serialize($attributes);
            }
        }

        return $result;
    }

    private function processAttributeImage(array &$attribute): void
    {
        foreach ($attribute as &$image) {
            if (is_array($image)) {
                $newImages = [$image['value'], $image['thumb']];
                $this->replaceByPatternApplier->execute(
                    ProductGalleryReplace::REPLACE_PATTERN_GROUP,
                    $newImages
                );
                $image['value'] = $newImages[0];
                $image['thumb'] = $newImages[1];
            }
        }
    }
}
