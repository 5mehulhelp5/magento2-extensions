<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image\Utils;

use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;

/**
 * Resolves image preg_match_all paths based on replace pattern configuration
 */
class PathsResolver
{
    /**
     * @param ReplaceConfigInterface $replaceConfig
     * @param array $images output of preg_match_all matched pattern images
     * @param int $index number of image in a group array
     *
     * @return array
     */
    public function resolve(ReplaceConfigInterface $replaceConfig, array $images, int $index): array
    {
        $result = [];
        foreach ($replaceConfig->getGroupByName() as $groupName => $groupNumber) {
            if (!empty($images[$groupName][$index])) {
                $result[] = $images[$groupNumber][$index];
                if (!$replaceConfig->isReplaceAllAttrs()) {
                    break;
                }
            }
        }

        return $result;
    }
}
