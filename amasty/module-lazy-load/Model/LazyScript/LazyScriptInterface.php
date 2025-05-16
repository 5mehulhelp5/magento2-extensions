<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\LazyScript;

interface LazyScriptInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getCode(): string;
}
