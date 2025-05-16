<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Image;

use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;

class ReplacePatternGroupsPool
{
    /**
     * @var ReplaceConfigInterface[][]
     */
    private $replacePatternGroups;

    /**
     * @param ReplaceConfigInterface[][] $replacePatternGroups
     */
    public function __construct(
        array $replacePatternGroups = []
    ) {
        $this->initializeReplacePatternGroups($replacePatternGroups);
    }

    /**
     * Get set of replace patterns used for image processing
     * in particular case (ex. ImageOptimizer replacement, LazyLoad replacement)
     *
     * @return ReplaceConfigInterface[]
     */
    public function getByKey(string $key): array
    {
        return $this->replacePatternGroups[$key] ?? [];
    }

    /**
     * @param ReplaceConfigInterface[][] $replacePatternGroups
     */
    private function initializeReplacePatternGroups(array $replacePatternGroups): void
    {
        foreach ($replacePatternGroups as $key => $group) {
            foreach ($group as $replacePattern) {
                if (!$replacePattern instanceof ReplaceConfigInterface) {
                    throw new \LogicException(
                        sprintf('Replace Pattern config must implement %s', ReplaceConfigInterface::class)
                    );
                }
            }
            $this->replacePatternGroups[$key] = $group;
        }
    }
}
