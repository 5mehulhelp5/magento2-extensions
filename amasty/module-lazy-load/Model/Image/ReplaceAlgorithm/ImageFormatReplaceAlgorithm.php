<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Image\ReplaceAlgorithm;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\BestReplaceAlgorithm;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\Checker\AlgorithmCheckerInterface;

/**
 * The same as BestReplace algorithm apart from using additional check for webp and avif image.
 * Check can't be done in checker but only at the execution moment.
 * Uses for ajax gallery and swatches.
 */
class ImageFormatReplaceAlgorithm extends BestReplaceAlgorithm
{
    public const ALGORITHM_NAME = 'lazyload_replace_with_image_format';

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var LazyConfig
     */
    private $lazyConfig;

    public function __construct(
        AlgorithmCheckerInterface $checker,
        OutputImage $outputImage,
        DeviceDetect $deviceDetect,
        LazyConfigFactory $replaceConfigFactory
    ) {
        parent::__construct($checker, $outputImage, $deviceDetect);
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
        $this->lazyConfig = $replaceConfigFactory->create();
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        if ($this->lazyConfig->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)) {
            $outputImage = $this->outputImage->initialize($imagePath);
            if ($outputImage->process() && ($outputImage->getWebpPath() || $outputImage->getAvifPath())) {
                $imagePath = $outputImage->getBest(...$this->deviceDetect->getDeviceParams());
            }
        }

        return $imagePath;
    }

    public function getAlgorithmName(): string
    {
        return self::ALGORITHM_NAME;
    }
}
