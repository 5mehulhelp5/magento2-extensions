<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Email\Alert;

use Magento\Email\Model\Template as EmailTemplate;

/**
 * Prevent double applying emulation.
 * Emulation must be applied before executing of this class.
 */
class Template extends EmailTemplate
{
    /**
     * @return bool
     */
    protected function applyDesignConfig()
    {
        return false;
    }

    /**
     * @return Template
     */
    protected function cancelDesignConfig()
    {
        return $this;
    }
}
