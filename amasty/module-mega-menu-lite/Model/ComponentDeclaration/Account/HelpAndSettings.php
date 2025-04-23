<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration\Account;

use Amasty\MegaMenuLite\Api\Component\NameProviderInterface;
use Amasty\MegaMenuLite\Api\Component\VisibilityInterface;
use Amasty\MegaMenuLite\Model\DataProvider\Currency;
use Amasty\MegaMenuLite\Model\DataProvider\Switcher;
use Magento\Framework\DataObject;

class HelpAndSettings extends DataObject
{
    /**
     * @var Currency
     */
    private $currencyDataProvider;

    /**
     * @var Switcher
     */
    private $storeSwitcherDataProvider;

    /**
     * @var VisibilityInterface|null
     */
    private $visibility;

    /**
     * @var NameProviderInterface|null
     */
    private $nameProvider;

    public function __construct(
        Currency $currencyDataProvider,
        Switcher $storeSwitcherDataProvider,
        ?VisibilityInterface $visibility = null,
        ?NameProviderInterface $nameProvider = null,
        array $data = []
    ) {
        parent::__construct($data);
        $this->currencyDataProvider = $currencyDataProvider;
        $this->storeSwitcherDataProvider = $storeSwitcherDataProvider;
        $this->visibility = $visibility;
        $this->nameProvider = $nameProvider;
    }

    public function isVisible(): bool
    {
        return $this->visibility === null || $this->visibility->isVisible();
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData('sort_order');
    }

    public function getItemData(): array
    {
        $data = $this->getData();
        $elems = $data['elems'];
        $elems['currency']['elems'] = [$this->getCurrencyElementData()];
        $elems['language']['elems'] = [$this->getLanguageElementData()];

        return array_merge(
            $data,
            [
                'isVisible' => $this->isVisible(),
                'elems' => array_values($elems),
                'name' => $this->getName(),
            ]
        );
    }

    public function getName(): string
    {
        return $this->nameProvider === null ? (string)$this->getData('name') : $this->nameProvider->getName();
    }

    public function getCurrencyElementData(): array
    {
        return $this->modifySettings($this->currencyDataProvider->getData());
    }

    public function getLanguageElementData(): array
    {
        return $this->modifySettings($this->storeSwitcherDataProvider->getData());
    }

    private function modifySettings(array $settings): array
    {
        $availableOptions = array_filter($settings['items'], function ($item) use ($settings) {
            return $item['code'] !== $settings['current_code'];
        });

        foreach ($availableOptions as $key => $option) {
            $availableOptions[$key]['id'] = $option['code'];
            $availableOptions[$key]['counter'] = $option['code'];
            $availableOptions[$key]['url'] = $option['url'] ?? $option['data-post'];
        }

        return [
            'id' => $settings['current_code'],
            'name' => $settings['current_name'] ?? $settings['current_code'],
            'counter' => $settings['current_code'],
            'elems' => array_values($availableOptions)
        ];
    }
}
