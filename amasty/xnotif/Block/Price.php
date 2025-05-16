<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */
namespace Amasty\Xnotif\Block;

/**
 * Class Price
 */
class Price extends AbstractBlock
{

    public function _construct()
    {
        $this->setAlertType("price");
        parent::_construct();
    }
}
