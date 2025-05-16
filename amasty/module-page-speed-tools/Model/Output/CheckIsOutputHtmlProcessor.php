<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output;

class CheckIsOutputHtmlProcessor implements OutputProcessorInterface
{
    public function process(string &$output): bool
    {
        if (preg_match('/(<html[^>]*>)(?>.*?<body[^>]*>)/is', $output)) {
            if (preg_match('/(<\/body[^>]*>)(?>.*?<\/html[^>]*>)/is', $output)) {
                return true;
            }
        }

        return false;
    }
}
