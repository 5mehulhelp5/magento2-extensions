<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxCart
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\AjaxCart\Model\Observer;

use Bss\AjaxCart\Helper\Data;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ProductAddToCartAfter implements ObserverInterface
{
    /**
     * Ajax cart helper.
     *
     * @var Data
     */
    protected $helper;

    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Initialize dependencies.
     *
     * @param Data $helper
     * @param Registry $registry
     */
    public function __construct(
        Data     $helper,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * Check is show additional data in quick view.
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helper->isEnabled()) {
            $resultItem = $observer->getQuoteItem();
            $this->registry->unregister('last_added_quote_item');
            $this->registry->register('last_added_quote_item', $resultItem);
        }
    }
}
