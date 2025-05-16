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

class ReplaceAlgorithmResolver
{
    /**
     * @var ReplaceAlgorithmInterface
     */
    private $defaultAlgorithm;

    /**
     * @var ReplaceAlgorithmInterface[]
     */
    private $replaceAlgorithms;

    /**
     * @param ReplaceAlgorithmInterface $defaultAlgorithm
     * @param ReplaceAlgorithmInterface[] $replaceAlgorithms
     */
    public function __construct(
        ReplaceAlgorithmInterface $defaultAlgorithm,
        array $replaceAlgorithms = []
    ) {
        $this->initializeReplaceAlgorithms($replaceAlgorithms);
        $this->defaultAlgorithm = $defaultAlgorithm;
    }

    /**
     * Resolves available algorithm based on replace pattern configuration.
     * If available algorithm can override base -- use it, otherwise use pattern base or default if no base exist
     */
    public function resolve(ReplaceConfigInterface $replacePattern): ReplaceAlgorithmInterface
    {
        foreach ($this->replaceAlgorithms as $algorithm) {
            if ($replacePattern->getBaseAlgorithm()
                && !$algorithm->canOverride()
                && $algorithm->getAlgorithmName() !== $replacePattern->getBaseAlgorithm()
            ) {
                continue;
            }
            if ($algorithm->isAvailable()) {
                return $algorithm;
            }
        }

        return $this->replaceAlgorithms[$replacePattern->getBaseAlgorithm()] ?? $this->defaultAlgorithm;
    }

    private function initializeReplaceAlgorithms(array $replaceAlgorithms): void
    {
        foreach ($replaceAlgorithms as $name => $algorithm) {
            if (!$algorithm instanceof ReplaceAlgorithmInterface) {
                throw new \LogicException(
                    sprintf('Image Replace Algorithm must implement %s', ReplaceAlgorithmInterface::class)
                );
            }
            $this->replaceAlgorithms[$name] = $algorithm;
        }
    }
}
