<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplacePatterns;

/**
 * Basic realization of replace config interface.
 * Can be used for instances creation through the virtual types
 * if no additional processing is needed
 */
class Common implements ReplaceConfigInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $groupByName;

    /**
     * @var bool
     */
    protected $replaceAllAttrs;

    /**
     * @var string
     */
    private $baseAlgorithm;

    public function __construct(
        string $name,
        string $pattern,
        array $groupByName,
        bool $replaceAllAttrs,
        string $baseAlgorithm = ''
    ) {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->groupByName = $groupByName;
        $this->replaceAllAttrs = $replaceAllAttrs;
        $this->baseAlgorithm = $baseAlgorithm;
    }

    public function getPatternName(): string
    {
        return $this->name;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getGroupByName(): array
    {
        return $this->groupByName;
    }

    public function isReplaceAllAttrs(): bool
    {
        return $this->replaceAllAttrs;
    }

    public function getBaseAlgorithm(): ?string
    {
        return $this->baseAlgorithm;
    }
}
