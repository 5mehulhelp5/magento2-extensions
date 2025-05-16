<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Catalog\Block\Product\View\Gallery as ImageGallery;
use Magento\Framework\Serialize\Serializer\Json;

class ProductGalleryReplace
{
    public const REPLACE_PATTERN_GROUP = 'gallery';

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        ReplaceByPatternApplier $replaceByPatternApplier,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceConfigFactory $replaceConfigFactory,
        Json $jsonSerializer
    ) {
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
        $this->jsonSerializer = $jsonSerializer;
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetGalleryImagesJson(ImageGallery $subject, string $result): string
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);
        if (!$this->lazyConfigProvider->get()->getData('is_lazy')
            && $replaceStrategy !== ReplaceStrategies::NONE
        ) {
            $imagesSettings = $this->jsonSerializer->unserialize($result);
            foreach ($imagesSettings as &$imagesSetting) {
                $this->replaceByPatternApplier->execute(self::REPLACE_PATTERN_GROUP, $imagesSetting);
            }
            $result = $this->jsonSerializer->serialize($imagesSettings);
        }

        return $result;
    }
}
