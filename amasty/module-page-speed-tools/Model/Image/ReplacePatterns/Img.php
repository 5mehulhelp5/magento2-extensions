<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\ReplacePatterns;

abstract class Img extends Common
{
    public const NAME = 'img';
    public const PATTERN = '<img\s*(?:%img_attributes%|(?<any>[a-z\-_]+\s*\=\s*[\'\"](.*?)[\'\"].*?\s*))+.*?>';

    public function __construct(
        string $name = self::NAME,
        string $pattern = self::PATTERN,
        array $groupByName = [],
        bool $replaceAllAttrs = false,
        string $baseAlgorithm = ''
    ) {
        parent::__construct($name, $pattern, $groupByName, $replaceAllAttrs, $baseAlgorithm);
        $this->updatePatternAndGroup($pattern);
    }

    /**
     * Must implement logic of supported 3rd-party attributes retrieving
     */
    abstract protected function retrieveImgAttributes(): array;

    /**
     * Additional processing for 3rd party attributes support compatibility on img tag replace pattern
     * need to re-configure pattern and group by name
     */
    private function updatePatternAndGroup(string $pattern)
    {
        $imgAttributes = $this->retrieveImgAttributes();
        $imgAttributes[] = 'src'; // the src group will be last each time
        $groupsByName = [];
        $imgAttributesRegexp = '';
        $groupNumber = 0;
        foreach ($imgAttributes as $index => $imgAttribute) {
            $groupName = str_replace('-', '_', $imgAttribute);
            $groupNumber += 2;
            $imgAttributesRegexp .= ($index ? '|' : '')
                . '(?<' . $groupName . '>'
                . $imgAttribute . '\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)';

            $groupsByName[$groupName] = $groupNumber;
        }
        $pattern = str_replace('%img_attributes%', $imgAttributesRegexp, $pattern);

        $this->pattern = $pattern;
        $this->groupByName = $groupsByName;
    }
}
