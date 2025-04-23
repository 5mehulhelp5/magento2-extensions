<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\ComponentDeclaration\Name;

use Amasty\MegaMenu\Model\ConfigProvider;
use Amasty\MegaMenuLite\Api\Component\NameProviderInterface;

class HelpAndSettings implements NameProviderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function getName(): string
    {
        return $this->configProvider->getHelpAndSettingsTabName();
    }
}
