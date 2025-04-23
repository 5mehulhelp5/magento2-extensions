<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Backend\Ui;

use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\ArrayManager;

class HideMobileFieldset
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ArrayManager $arrayManager,
        Manager $moduleManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->moduleManager = $moduleManager;
    }

    public function execute(array $meta): array
    {
        if (!$this->moduleManager->isEnabled('Amasty_MegaMenuPremium')) {
            $path = sprintf('%s/arguments/data/config/visible', 'am_mega_menu_mobile_fieldset');
            $meta = $this->arrayManager->set($path, $meta, false);
        }

        return $meta;
    }
}
