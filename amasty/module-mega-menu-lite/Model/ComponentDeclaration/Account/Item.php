<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration\Account;

use Amasty\MegaMenuLite\Api\Component\NameProviderInterface;
use Amasty\MegaMenuLite\Api\Component\UrlProviderInterface;
use Amasty\MegaMenuLite\Api\Component\VisibilityInterface;
use Magento\Framework\DataObject;

class Item extends DataObject
{
    /**
     * @var UrlProviderInterface
     */
    private $urlProvider;

    /**
     * @var VisibilityInterface|null
     */
    private $visibility;

    /**
     * @var NameProviderInterface|null
     */
    private $nameProvider;

    public function __construct(
        UrlProviderInterface $urlProvider,
        ?VisibilityInterface $visibility = null,
        ?NameProviderInterface $nameProvider = null,
        array $data = []
    ) {
        $this->urlProvider = $urlProvider;
        $this->visibility = $visibility;
        $this->nameProvider = $nameProvider;
        parent::__construct($data);
    }

    public function isVisible(): bool
    {
        return $this->visibility === null || $this->visibility->isVisible();
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData('sort_order');
    }

    public function getName(): string
    {
        return $this->nameProvider === null ? (string)$this->getData('name') : $this->nameProvider->getName();
    }

    public function getItemData(): array
    {
        return array_merge(
            $this->getData(),
            [
                'isVisible' => $this->isVisible(),
                'url' => $this->urlProvider->getUrl($this->getData('url')),
                'name' =>$this->getName()
            ]
        );
    }
}
