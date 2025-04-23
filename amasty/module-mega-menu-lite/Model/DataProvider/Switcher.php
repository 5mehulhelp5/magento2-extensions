<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\DataProvider;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Block\Switcher as SwitcherBlock;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\ViewModel\SwitcherUrlProvider;

class Switcher implements ArgumentInterface
{
    /**
     * @var SwitcherBlock
     */
    private $switcherBlock;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SwitcherUrlProvider
     */
    private $switcherUrlProvider;

    public function __construct(
        SwitcherBlock $switcherBlock,
        StoreManagerInterface $storeManager,
        SwitcherUrlProvider $switcherUrlProvider
    ) {
        $this->switcherBlock = $switcherBlock;
        $this->storeManager = $storeManager;
        $this->switcherUrlProvider = $switcherUrlProvider;
    }

    public function getData(): array
    {
        $store = $this->storeManager->getStore();
        $data = [
            'current_code' => $store->getCode(),
            'current_name' => $store->getName(),
            'current_store_id' => $store->getId(),
            'items' => []
        ];

        foreach ($this->switcherBlock->getStores() as $store) {
            $data['items'][] = [
                'url' => $this->switcherUrlProvider->getTargetStoreRedirectUrl($store),
                'code' => $store->getCode(),
                'name' => $store->getName()
            ];
        }

         return $data;
    }
}
