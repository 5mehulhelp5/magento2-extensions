<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\CatalogGraphQl\Model\Resolver\Product\MediaGallery\Url;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\CatalogGraphQl\Model\Resolver\Product\MediaGallery\Url;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CatalogGraphQlGalleryReplace
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

    /**
     *
     * @var array
     */
    private $typesForProcess;

    public function __construct(
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory,
        array $typesForProcess
    ) {
        $this->replaceByPatternApplier = $replaceByPatternApplier;
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->typesForProcess = $typesForProcess;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterResolve(
        Url $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if ($this->lazyConfigProvider->get()->getData('is_lazy')
            || $replaceStrategy === ReplaceStrategies::NONE
        ) {
            return $result;
        }

        foreach ($this->typesForProcess as $type) {
            if (in_array($type, $info->path)) {
                $imageUrl = [$result];
                $this->replaceByPatternApplier->execute(
                    ProductGalleryReplace::REPLACE_PATTERN_GROUP,
                    $imageUrl
                );

                return $imageUrl[0];
            }
        }

        return $result;
    }
}
