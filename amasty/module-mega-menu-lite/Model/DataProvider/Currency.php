<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\DataProvider;

use Magento\Directory\Block\Currency as CurrencyBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Currency implements ArgumentInterface
{
    /**
     * @var CurrencyBlock
     */
    private $currencyBlock;

    public function __construct(CurrencyBlock $currencyBlock)
    {
        $this->currencyBlock = $currencyBlock;
    }

    public function getData(): array
    {
        $data = [
            'current_code' => $this->currencyBlock->getCurrentCurrencyCode(),
            'current_code_title' => $this->currencyBlock->getCurrentCurrencyCode(),
            'current_name' => $this->getCurrentCurrencyName(),
            'id_modifier' => 'nav',
            'items' => []
        ];

        foreach ($this->currencyBlock->getCurrencies() as $code => $name) {
            $data['items'][] = [
                'data-post' => $this->currencyBlock->getSwitchCurrencyPostData($code),
                'name' => $name,
                'code' => $code,
                'code_title' => $code
            ];
        }

        return $data;
    }

    private function getCurrentCurrencyName(): ?string
    {
        $current = $this->currencyBlock->getCurrentCurrencyCode();
        foreach ($this->currencyBlock->getCurrencies() as $code => $name) {
            if ($code == $current) {
                return $name;
            }
        }

        return null;
    }
}
