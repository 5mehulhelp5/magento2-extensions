<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Block\Widget;

use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;

class Wrapper extends Template implements BlockInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Manager $moduleManager,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toHtml(): string
    {
        if ($this->moduleManager->isEnabled('Amasty_MegaMenuPremium')) {
            $widget = $this->getLayout()->createBlock(
                Banner::class
            )->setData(
                $this->getData()
            );

            $html = $widget->toHtml();
        }

        return $html ?? '';
    }
}
