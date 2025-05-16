<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Faq\Model\ThirdParty\ModuleChecker;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleChecker $moduleChecker
    ) {
        $this->objectManager = $objectManager;
        $this->moduleChecker = $moduleChecker;
    }

    public function apply()
    {
        if ($this->moduleChecker->isAmastyGdprFaqSampleDataEnabled()
            && $this->moduleChecker->isAmastyGdprEnabled()
        ) {
            $this->getSampleDataInstaller()->install();
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    private function getSampleDataInstaller(): Installer
    {
        return $this->objectManager->get(Installer::class);
    }
}
