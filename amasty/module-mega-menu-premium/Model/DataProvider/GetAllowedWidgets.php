<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\DataProvider;

class GetAllowedWidgets
{
    /**
     * @var array|null
     */
    private $allowedWidgets;

    public function __construct(?array $allowedWidgets = [])
    {
        $this->allowedWidgets = $allowedWidgets;
    }

    public function execute(): array
    {
        return $this->allowedWidgets;
    }
}
