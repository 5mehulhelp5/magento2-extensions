<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Block\Adminhtml\Builder\Store;

class Switcher extends \Magento\Backend\Block\Store\Switcher
{
    /**
     * @var bool
     */
    protected $_hasDefaultOption = false;

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        $storeId = parent::getStoreId();
        if (!$storeId) {
            $storeId = $this->_storeManager->getDefaultStoreView()->getId();
        }

        return $storeId;
    }
}
