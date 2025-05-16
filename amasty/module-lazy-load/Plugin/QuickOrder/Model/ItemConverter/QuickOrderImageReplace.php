<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Plugin\QuickOrder\Model\ItemConverter;

use Amasty\LazyLoad\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Amasty\QuickOrder\Model\ItemConverter;

class QuickOrderImageReplace
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
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigFactory $lazyConfigFactory
    ) {
        $this->lazyConfig = $lazyConfigFactory->create();
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(ItemConverter $subject, array $result): array
    {
        if (!$this->lazyConfig->getData(LazyConfig::IS_LAZY)) {
            return $result;
        }

        if (!empty($result['image'])) {
            $image = [$result['image']];
            $this->replaceByPatternApplier->execute(ProductGalleryReplace::REPLACE_PATTERN_GROUP, $image);
            $result['image'] = $image[array_key_first($image)];
        }

        return $result;
    }
}
