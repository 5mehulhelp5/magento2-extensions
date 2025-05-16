<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Plugin\Swatches\Helper\Data;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\LazyLoad\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Swatches\Helper\Data;

class SwatchImageReplace
{
    /**
     * @var LazyConfig
     */
    private $lazyConfig;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        LazyConfigFactory $lazyConfigFactory,
        ReplaceByPatternApplier $replaceByPatternApplier
    ) {
        $this->lazyConfig = $lazyConfigFactory->create();
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductMediaGallery(Data $helper, array $result): array
    {
        if (!empty($result) && $this->lazyConfig->getData(LazyConfig::IS_LAZY)) {
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
