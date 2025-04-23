<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Banner extends Template implements BlockInterface, IdentityInterface
{
    public const CACHE_TAG = 'am_megamenu_banner';

    /**
     * @var string
     */
    protected $_template = 'Amasty_MegaMenuPremium::widget/banner.phtml';

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return str_replace('\\', '-', $this->getNameInLayout());
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    public function getFullImageUrl(?string $image): ?string
    {
        return $image ? $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB) . $image : '';
    }
}
