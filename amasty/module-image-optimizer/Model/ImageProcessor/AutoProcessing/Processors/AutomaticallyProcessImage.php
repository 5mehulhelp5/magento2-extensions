<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\Processors;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Image\ImageSettingGetter;
use Amasty\ImageOptimizer\Model\ImageProcessor;

class AutomaticallyProcessImage implements AutoProcessorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var ImageSettingGetter
     */
    private $imageSettingGetter;

    public function __construct(
        ImageProcessor $imageProcessor,
        ConfigProvider $configProvider,
        ImageSettingGetter $imageSettingGetter
    ) {
        $this->configProvider = $configProvider;
        $this->imageProcessor = $imageProcessor;
        $this->imageSettingGetter = $imageSettingGetter;
    }

    public function execute(string $imgPath): void
    {
        if (!$this->configProvider->isAutomaticallyOptimizeImages()
            || ($imageSetting = $this->imageSettingGetter->getSettingByImgPath($imgPath)) === null
            || !$imageSetting->isAutomaticOptimization()
        ) {
            return;
        }

        if ($queueImage = $this->imageProcessor->prepareQueue($imgPath, $imageSetting)) {
            $this->imageProcessor->process($queueImage);
        }
    }
}
