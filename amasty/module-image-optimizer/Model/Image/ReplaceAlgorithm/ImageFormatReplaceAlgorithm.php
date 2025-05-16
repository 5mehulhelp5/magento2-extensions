<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image\ReplaceAlgorithm;

use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
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
    public const ALGORITHM_NAME = 'replace_with_image_format';

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    public function __construct(
        AlgorithmCheckerInterface $checker,
        OutputImage $outputImage,
        DeviceDetect $deviceDetect,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        parent::__construct($checker, $outputImage, $deviceDetect);
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
        $this->replaceConfig = $replaceConfigFactory->create();
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        if ($this->replaceConfig->getData(ReplaceConfig::IS_REPLACE_WITH_USER_AGENT)) {
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
