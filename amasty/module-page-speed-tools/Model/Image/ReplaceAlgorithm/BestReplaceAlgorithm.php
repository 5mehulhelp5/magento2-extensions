<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm;

use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\Image\OutputImage;

class BestReplaceAlgorithm implements ReplaceAlgorithmInterface
{
    public const ALGORITHM_NAME = 'replace_with_best';

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var Checker\AlgorithmCheckerInterface
     */
    private $checker;

    public function __construct(
        Checker\AlgorithmCheckerInterface $checker,
        OutputImage $outputImage,
        DeviceDetect $deviceDetect
    ) {
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
        $this->checker = $checker;
    }

    public function execute(string $image, string $imagePath): string
    {
        $replacedImagePath = $this->getReplaceImagePath($imagePath);
        if ($replacedImagePath != $imagePath) {
            return str_replace(
                $imagePath,
                $replacedImagePath,
                $image
            );
        }

        return $image;
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);
        if ($outputImage->process()) {
            $imagePath = $outputImage->getBest(...$this->deviceDetect->getDeviceParams());
        }

        return $imagePath;
    }

    public function getAlgorithmName(): string
    {
        return self::ALGORITHM_NAME;
    }

    public function isAvailable(): bool
    {
        return $this->checker->check();
    }

    public function canOverride(): bool
    {
        return false;
    }
}
