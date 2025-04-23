<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Block\Adminhtml\Link;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Backend\DataProvider\LinkRegistry;
use Magento\Framework\UrlInterface;

class GenericButton
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var LinkRegistry
     */
    private $registry;

    public function __construct(
        UrlInterface $urlBuilder,
        LinkRegistry $registry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
    }

    public function getUrlBuilder(): UrlInterface
    {
        return $this->urlBuilder;
    }

    /**
     * @return null|int
     */
    public function getLinkId(): ?int
    {
        $link = $this->registry->getLink();

        return $link ? $link->getEntityId() : null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     *
     * @return  string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
