<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Content;

class Matrix
{
    public function getNewColumnIndexes(int $nodesCount, int $columnsCount, int $level): array
    {
        $result = [];
        if ($level !== 1 || $nodesCount <= $columnsCount) {
            return $result;
        }

        $itemPerPage = ceil($nodesCount / $columnsCount) ?: 1;
        $counter = 1;
        $tempMatrix = [];

        for ($i = 0; $i <= $itemPerPage; $i++) {
            for ($j = 0; $j < $columnsCount; $j++) {
                if ($counter <= $nodesCount) {
                    $tempMatrix[$i][$j] = $counter++;
                } else {
                    break;
                }
            }
        }

        // matrix transposition
        $tempMatrix = array_map(null, ...$tempMatrix);
        foreach ($tempMatrix as $key => $value) {
            if (is_array($value)) {
                $value = array_filter($value); // remove null elements
            }

            $result[] = end($result) + count($value);
        }

        return $result;
    }
}
