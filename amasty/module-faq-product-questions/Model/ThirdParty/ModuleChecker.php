<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\ThirdParty;

use Magento\Framework\Module\Manager;

class ModuleChecker
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function isAmastyGdprEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_Gdpr');
    }

    public function isAmastyGdprFaqSampleDataEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Amasty_GdprFaqSampleData');
    }
}
