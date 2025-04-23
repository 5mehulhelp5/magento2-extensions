<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Block;

use Amasty\MegaMenuLite\Model\ComponentDeclaration\DeclarationPool;
use Amasty\MegaMenuLite\ViewModel\Tree;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Url as CustomerUrlModel;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class Container extends Template
{
    /**
     * @var array
     */
    private $jsConfig = [];

    /**
     * @var Json
     */
    private $json;

    /**
     * @var CustomerUrlModel
     */
    private $customerUrlModel;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var DeclarationPool
     */
    private $componentDeclarationPool;

    public function __construct(
        Template\Context $context,
        Json $json,
        CustomerUrlModel $customerUrlModel,
        HttpContext $httpContext,
        Tree $tree,
        array $data = [],
        DeclarationPool $componentDeclarationPool = null // TODO: move to not optional
    ) {
        parent::__construct($context, $data);
        $this->json = $json;
        $this->customerUrlModel = $customerUrlModel;
        $this->httpContext = $httpContext;
        $this->tree = $tree;
        $this->componentDeclarationPool = $componentDeclarationPool
            ?? ObjectManager::getInstance()->get(DeclarationPool::class);
    }

    public function getJsComponents()
    {
        $jsLayout = $this->getData('jsLayout');
        $this->jsLayout = $jsLayout['components'] ?? [];

        return $this->json->serialize($this->jsLayout);
    }

    public function getComponentsDeclaration()
    {
        return $this->json->serialize($this->componentDeclarationPool->getComponentDeclarations());
    }

    public function getJsSettings()
    {
        $settings = [
            'account' => [
                'is_logged_in' => $this->isLoggedIn(),
                'login' => $this->customerUrlModel->getLoginUrl(),
                'create' => $this->customerUrlModel->getRegisterUrl(),
                'logout' => $this->customerUrlModel->getLogoutUrl(),
                'account' => $this->customerUrlModel->getAccountUrl()
            ]
        ];

        $layoutSettings = $this->getData('jsLayout')['settings'] ?? [];
        foreach ($layoutSettings as $key => $layoutSettingModel) {
            $settings[$key] = $layoutSettingModel->getData();
        }

        return $this->json->serialize($settings);
    }

    public function getStoreLinks(): string
    {
        $block = $this->getLayout()->getBlock('store.links');
        if ($block) {
            $data = $block->getData();
        }

        return $this->json->serialize($data ?? []);
    }

    public function getJsConfig(): array
    {
        if (!$this->jsConfig) {
            $settings = [];
            $configs = $this->getData('jsLayout')['config'] ?? [];

            foreach ($configs as $config) {
                $config->modifyConfig($settings);
            }
            $this->jsConfig = $settings;
        }

        return $this->jsConfig;
    }

    public function getSerializedJsConfig(): string
    {
        return $this->json->serialize($this->getJsConfig());
    }

    public function getJsData(): string
    {
        return $this->json->serialize($this->getNodesData());
    }

    /**
     * Is customer logged in
     *
     * @return bool
     */
    private function isLoggedIn(): bool
    {
        return (bool) $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getNodesData(): array
    {
        return $this->tree->getNodesData();
    }

    public function getAllNodesData(): array
    {
        return $this->tree->getAllNodesData();
    }

    public function getHamburgerNodesData(): array
    {
        return $this->tree->getHamburgerNodesData();
    }

    public function getToggleMenuText(): string
    {
        return __('Toggling menu')->render();
    }
}
