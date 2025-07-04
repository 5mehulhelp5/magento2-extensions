<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\Backend\Model;

use Magento\Backend\Model\Url as BackendUrlModel;
use Magento\Framework\Registry;

/**
 * Class Url
 */
class Url
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param BackendUrlModel $subject
     * @param string $areaFrontName
     * @return string
     */
    public function afterGetAreaFrontName(BackendUrlModel $subject, $areaFrontName)
    {
        if ($this->registry->registry('xnotif_test_notification')) {
            $areaFrontName = '';
        }

        return $areaFrontName;
    }
}
