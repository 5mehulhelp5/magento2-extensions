<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Observer\Layout;

use Amasty\MegaMenuLite\Model\ConfigProvider;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class Handle implements ObserverInterface
{
    public const CHECKOUT_HANDLE = 'checkout_index_index';
    public const AMCHECKOUT_HEADERFOOTER_HANDLE = 'amasty_checkout_headerfooter';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function execute(Observer $observer): void
    {
        /** @var LayoutInterface $layout */
        $layout = $observer->getEvent()->getLayout();

        if ($layout && $this->configProvider->isEnabled() && $this->isAmMegaMenuHandleAllowed($layout)) {
            $layout->getUpdate()->addHandle('am_mega_menu_layout');
        }
    }

    /**
     * @param LayoutInterface $layout
     *
     * @return bool
     */
    private function isAmMegaMenuHandleAllowed(LayoutInterface $layout): bool
    {
        $handles = $layout->getUpdate()->getHandles();

        if (in_array(self::CHECKOUT_HANDLE, $handles)
            && !in_array(self::AMCHECKOUT_HEADERFOOTER_HANDLE, $handles)
        ) {
            return false;
        }

        return true;
    }
}
