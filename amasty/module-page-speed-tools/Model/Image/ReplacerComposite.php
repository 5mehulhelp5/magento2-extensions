<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image;

use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithm\ReplaceAlgorithmInterface;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;

class ReplacerComposite implements ReplacerCompositeInterface
{
    /**
     * @var string[]
     */
    private $imagePathCache = [];

    /**
     * @var ReplaceAlgorithmInterface[]
     */
    private $patternAlgorithmsMap = [];

    /**
     * @var ReplaceAlgorithmResolver
     */
    private $replaceAlgorithmResolver;

    public function __construct(
        ReplaceAlgorithmResolver $replaceAlgorithmResolver
    ) {
        $this->replaceAlgorithmResolver = $replaceAlgorithmResolver;
    }

    public function replace(ReplaceConfigInterface $replaceConfig, string $image, string $imagePath): string
    {
        $algorithm = $this->resolveAlgorithm($replaceConfig);
        $imagePath = $this->prepareImagePath($imagePath);

        return $algorithm->execute($image, $imagePath);
    }

    public function replaceImagePath(ReplaceConfigInterface $replaceConfig, string $imagePath): string
    {
        if (!isset($this->imagePathCache[$imagePath])) {
            $algorithm = $this->resolveAlgorithm($replaceConfig);
            $imagePath = $this->prepareImagePath($imagePath);
            $this->imagePathCache[$imagePath] = $algorithm->getReplaceImagePath($imagePath);
        }

        return $this->imagePathCache[$imagePath];
    }

    private function resolveAlgorithm(ReplaceConfigInterface $replaceConfig): ReplaceAlgorithmInterface
    {
        if (!isset($this->patternAlgorithmsMap[$replaceConfig->getPatternName()])) {
            $this->patternAlgorithmsMap[$replaceConfig->getPatternName()]
                = $this->replaceAlgorithmResolver->resolve($replaceConfig);
        }

        return $this->patternAlgorithmsMap[$replaceConfig->getPatternName()];
    }

    private function prepareImagePath(string $imagePath): string
    {
        return (string)strtok($imagePath, '?'); //remove get-parameters if they exists
    }
}
