<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Observer;

use Magento\Framework\Event\ObserverInterface;
use Amasty\ShopbyBase\Helper\Data;

class CategoryManagerInitAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->coreRegistry->registry(Data::SHOPBY_CATEGORY_INDEX)) {
            $this->coreRegistry->register(Data::SHOPBY_CATEGORY_INDEX, true);
        }
    }
}
