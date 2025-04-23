<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Block\Data\Form\Element;

class TextForceStaticPath extends \Magento\Framework\Data\Form\Element\Text
{
    /**
     * @var array
     */
    protected $customAttributes = ['data-force_static_path' => 1];
}
