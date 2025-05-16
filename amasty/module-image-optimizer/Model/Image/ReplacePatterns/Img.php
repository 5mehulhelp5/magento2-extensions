<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image\ReplacePatterns;

use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\Img as BaseImg;

class Img extends BaseImg
{
    /**
     * @var ReplaceConfigFactory
     */
    private $replaceConfigFactory;

    public function __construct(
        ReplaceConfigFactory $replaceConfigFactory,
        string $name = BaseImg::NAME,
        string $pattern = BaseImg::PATTERN,
        array $groupByName = [],
        bool $replaceAllAttrs = false,
        string $baseAlgorithm = ''
    ) {
        $this->replaceConfigFactory = $replaceConfigFactory;

        parent::__construct($name, $pattern, $groupByName, $replaceAllAttrs, $baseAlgorithm);
    }

    protected function retrieveImgAttributes(): array
    {
        return (array)$this->replaceConfigFactory->create()
            ->getData(ReplaceConfig::SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
    }
}
