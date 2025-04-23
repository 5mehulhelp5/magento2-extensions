<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\ComponentDeclaration\Visibility;

use Amasty\MegaMenu\Model\ConfigProvider;
use Amasty\MegaMenu\Model\OptionSource\HelpAndSettings as HelpAndSettingsOptions;
use Amasty\MegaMenuLite\Api\Component\VisibilityInterface;

class HelpAndSettings implements VisibilityInterface
{
    private const VISIBLE_STATES = [
        HelpAndSettingsOptions::BOTH
    ];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function isVisible(): bool
    {
        return in_array($this->configProvider->getHelpAndSettingsDisplay(), self::VISIBLE_STATES);
    }
}
