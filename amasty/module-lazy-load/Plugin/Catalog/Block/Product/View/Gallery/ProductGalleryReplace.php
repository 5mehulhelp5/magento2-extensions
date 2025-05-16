<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Plugin\Catalog\Block\Product\View\Gallery;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Catalog\Block\Product\View\Gallery as ImageGallery;
use Magento\Framework\Serialize\Serializer\Json;

class ProductGalleryReplace
{
    public const REPLACE_PATTERN_GROUP = 'lazyload_gallery';

    /**
     * @var LazyConfig
     */
    private $lazyConfig;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        LazyConfigFactory $lazyConfigFactory,
        ReplaceByPatternApplier $replaceByPatternApplier,
        Json $jsonSerializer
    ) {
        $this->lazyConfig = $lazyConfigFactory->create();
        $this->jsonSerializer = $jsonSerializer;
        $this->replaceByPatternApplier = $replaceByPatternApplier;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetGalleryImagesJson(ImageGallery $subject, string $result): string
    {
        if ($this->lazyConfig->getData(LazyConfig::IS_LAZY)) {
            $imagesSettings = $this->jsonSerializer->unserialize($result);
            foreach ($imagesSettings as &$imagesSetting) {
                $this->replaceByPatternApplier->execute(self::REPLACE_PATTERN_GROUP, $imagesSetting);
            }
            $result = $this->jsonSerializer->serialize($imagesSettings);
        }

        return $result;
    }
}
