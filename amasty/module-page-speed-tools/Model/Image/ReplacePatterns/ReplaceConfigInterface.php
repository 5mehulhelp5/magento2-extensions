<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplacePatterns;

interface ReplaceConfigInterface
{
    /**
     * Name of a pattern
     *
     * @return string
     */
    public function getPatternName(): string;

    /**
     * RegEx pattern to use in preg_match_all replacement
     *
     * @return string
     */
    public function getPattern(): string;

    /**
     * Map for attribute replacement
     *
     * @return array [replace attribute => expected preg_match_all output key]
     */
    public function getGroupByName(): array;

    /**
     * Default algorithm used for replacement.
     * If empty then can work win any.
     *
     * @return string|null
     */
    public function getBaseAlgorithm(): ?string;

    /**
     * Flag to mark to replace only first occurrence or all
     *
     * @return bool
     */
    public function isReplaceAllAttrs(): bool;
}
