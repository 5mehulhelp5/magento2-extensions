<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Mostviewed\Block\Widget\Related;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\Mostviewed\Block\Widget\Related;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Catalog\Block\Product\Image;

class MostviewedImageReplace
{
    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    public function __construct(
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->replaceByPatternApplier = $replaceByPatternApplier;
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetImage(Related $subject, Image $result): Image
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if (!$this->lazyConfigProvider->get()->getData('is_lazy')
            && $replaceStrategy !== ReplaceStrategies::NONE
        ) {
            $imageUrl = [$result->getImageUrl()];
            $this->replaceByPatternApplier->execute(
                ProductGalleryReplace::REPLACE_PATTERN_GROUP,
                $imageUrl
            );
            $result->setImageUrl($imageUrl[0]);
        }

        return $result;
    }
}
