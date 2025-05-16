<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Block\Navigation;

use Amasty\Shopby\Model\ConfigProvider;
use Amasty\Shopby\Model\UrlResolver\UrlResolverInterface;
use Magento\Framework\View\Element\Template;

/**
 * @api
 */
class JsInit extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'jsinit.phtml';

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    /**
     * @var UrlResolverInterface
     */
    private $urlResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        \Amasty\Shopby\Helper\Data $helper,
        UrlResolverInterface $urlResolver,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->urlResolver = $urlResolver;
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    /**
     *
     * @return int
     */
    public function collectFilters()
    {
        return (int)$this->helper->collectFilters();
    }

    /**
     * @return string
     */
    public function getClearUrl(): string
    {
        return $this->urlResolver->resolve();
    }

    /**
     * @return bool
     */
    public function getEnableStickySidebarDesktop(): bool
    {
        return $this->configProvider->isEnableStickySidebarDesktop();
    }
}
