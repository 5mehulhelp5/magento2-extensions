<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\PageBuilder\Model\Stage;

use Magento\PageBuilder\Model\Stage\Preview as StagePreview;

class Preview
{
    /**
     * @param StagePreview $subject
     * @param \Closure $proceed
     *
     * @return bool
     */
    public function aroundIsPreviewMode(StagePreview $subject, \Closure $proceed)
    {
        try {
            return $proceed();
        } catch (\TypeError $e) {
            return false;
        }
    }
}
