<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Image\ReplacePatterns;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\Img as BaseImg;

class Img extends BaseImg
{
    /**
     * @var LazyConfigFactory
     */
    private $lazyConfigFactory;

    public function __construct(
        LazyConfigFactory $lazyConfigFactory,
        string $name = BaseImg::NAME,
        string $pattern = BaseImg::PATTERN,
        array $groupByName = [],
        bool $replaceAllAttrs = false,
        string $baseAlgorithm = ''
    ) {
        $this->lazyConfigFactory = $lazyConfigFactory;

        parent::__construct($name, $pattern, $groupByName, $replaceAllAttrs, $baseAlgorithm);
    }

    protected function retrieveImgAttributes(): array
    {
        return (array)$this->lazyConfigFactory->create()
            ->getData(LazyConfig::IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
    }
}
