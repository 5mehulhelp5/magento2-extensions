<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\DataProvider;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;

class LinkRegistry
{
    /**
     * Registry collection
     *
     * @var array
     */
    private $registry = [];

    public function getLink(): ?LinkInterface
    {
        return $this->registry[LinkInterface::PERSIST_NAME] ?? null;
    }

    /**
     * Register a new variable
     *
     * @param LinkInterface LinkInterface
     * @return void
     * @throws \RuntimeException
     */
    public function registerLink(LinkInterface $value): void
    {
        if (isset($this->registry[LinkInterface::PERSIST_NAME])) {
            throw new \RuntimeException('Registry key "' . LinkInterface::PERSIST_NAME . '" already exists');
        }
        $this->registry[LinkInterface::PERSIST_NAME] = $value;
    }

    /**
     * @param int $storeId
     * @return void
     */
    public function registerStoreId(int $storeId): void
    {
        if (isset($this->registry[ItemInterface::STORE_ID])) {
            throw new \RuntimeException('Registry key "' . ItemInterface::STORE_ID . '" already exists');
        }
        $this->registry[ItemInterface::STORE_ID] = $storeId;
    }

    public function getStoreId(): ?int
    {
        return $this->registry[ItemInterface::STORE_ID] ?? null;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     * @return void
     */
    public function unregister(string $key): void
    {
        if (isset($this->registry[$key])) {
            unset($this->registry[$key]);
        }
    }
}
