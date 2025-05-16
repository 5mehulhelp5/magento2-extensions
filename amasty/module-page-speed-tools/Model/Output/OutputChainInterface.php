<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output;

interface OutputChainInterface
{
    /**
     * @param string $output
     *
     * @return bool
     */
    public function process(string &$output): bool;
}
