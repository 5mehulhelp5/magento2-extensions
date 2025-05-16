<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Controller;

use Amasty\ShopbySeo\Model\ConfigProvider as SeoConfigProvider;
use Magento\Framework\App\RequestInterface;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    /**
     * @var SeoConfigProvider
     */
    private $seoConfigProvider;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Amasty\Shopby\Helper\Data $helper,
        SeoConfigProvider $seoConfigProvider
    ) {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
        $this->seoConfigProvider = $seoConfigProvider;
    }

    /**
     * @param RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        if (!$this->helper->isAllProductsEnabled()) {
            return false;
        }

        $identifier = trim($request->getPathInfo(), '/');

        if ($this->seoConfigProvider->isAddSuffix()
            && ($seoSuffix = $this->helper->getCatalogSeoSuffix())
        ) {
            $suffixPosition = strpos($identifier, $seoSuffix);
            if ($suffixPosition !== false) {
                $identifier = substr($identifier, 0, $suffixPosition);
            }
        }

        if ($this->checkMatchExpressions($request, $identifier)) {
            $request->setModuleName('amshopby')
                ->setControllerName('index')
                ->setActionName('index')
                ->setAlias(
                    \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS,
                    $identifier
                );

            return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
        }

        return false;
    }

    /**
     * @param RequestInterface $request
     * @param string $identifier
     * @return bool
     */
    public function checkMatchExpressions(RequestInterface $request, $identifier)
    {
        return $identifier == $this->helper->getAllProductsUrlKey();
    }
}
