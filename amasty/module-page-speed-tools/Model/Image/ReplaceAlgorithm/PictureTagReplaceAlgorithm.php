<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm;

use Amasty\PageSpeedTools\Model\Image\OutputImage;

class PictureTagReplaceAlgorithm implements ReplaceAlgorithmInterface
{
    public const ALGORITHM_NAME = 'replace_with_picture';

    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var Checker\AlgorithmCheckerInterface
     */
    private $checker;

    public function __construct(
        Checker\AlgorithmCheckerInterface $checker,
        OutputImage $outputImage
    ) {
        $this->outputImage = $outputImage;
        $this->checker = $checker;
    }

    public function execute(string $image, string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);

        if ($outputImage->process() && $sourceSet = $outputImage->getSourceSet()) {
            return '<picture>' . $sourceSet . $image . '</picture>';
        }

        return $image;
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        return $imagePath;
    }

    public function isAvailable(): bool
    {
        return $this->checker->check();
    }

    public function getAlgorithmName(): string
    {
        return self::ALGORITHM_NAME;
    }

    public function canOverride(): bool
    {
        return false;
    }
}
