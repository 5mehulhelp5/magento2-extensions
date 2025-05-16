<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image;

class ReplaceByPatternApplier
{
    /**
     * @var ReplacePatternGroupsPool
     */
    private $replacePatternGroupsPool;

    /**
     * @var ReplacerCompositeInterface
     */
    private $replacerComposite;

    public function __construct(
        ReplacePatternGroupsPool $replacePatternGroupsPool,
        ReplacerCompositeInterface $replacerComposite
    ) {
        $this->replacePatternGroupsPool = $replacePatternGroupsPool;
        $this->replacerComposite = $replacerComposite;
    }

    public function execute(string $replacePatternGroupKey, array &$images): void
    {
        $replacePatterns = $this->replacePatternGroupsPool->getByKey($replacePatternGroupKey);
        foreach ($images as &$image) {
            foreach ($replacePatterns as $replacePattern) {
                if (is_string($image) && preg_match('/' . $replacePattern->getPattern() . '/', $image)) {
                    $newImage = $this->replacerComposite->replaceImagePath($replacePattern, $image);
                    $imagePath = $this->prepareImagePath($image);
                    $image = str_replace($imagePath, $newImage, $image);
                }
            }
        }
    }

    private function prepareImagePath(string $imagePath): string
    {
        return (string)strtok($imagePath, '?'); //remove get-parameters if they exists
    }
}
