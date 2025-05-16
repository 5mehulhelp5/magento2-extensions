<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\QuickOrder\Model\ItemConverter;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Amasty\QuickOrder\Model\ItemConverter;

class QuickOrderImageReplace
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
    public function afterConvert(ItemConverter $subject, array $result): array
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if ($this->lazyConfigProvider->get()->getData('is_lazy')
            || $replaceStrategy === ReplaceStrategies::NONE
        ) {
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
