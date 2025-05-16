<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule;

use Amasty\Shiprules\Api\Data\RuleInterface;
use Amasty\Shiprules\Api\RuleRepositoryInterface;
use Amasty\Shiprules\Model\Rule;
use Amasty\Shiprules\Model\Rule\Adjustment\Total\Registry as TotalRegistry;
use Amasty\Shiprules\Model\Rule\Items\Provider as ItemsProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\CustomerGroupProvider;
use Amasty\Shiprules\Model\Rule\Quote\Address\HashProvider;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\StoreManagerInterface;

class Provider
{
    /**
     * @var array ... => \Amasty\Shiprules\Model\Rule[]
     */
    private $storage = [];

    /**
     * @var State
     */
    private $appState;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var HashProvider
     */
    private $hashProvider;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var CustomerGroupProvider
     */
    private $groupProvider;

    /**
     * @var ItemsProvider
     */
    private $itemsProvider;

    /**
     * @var TotalRegistry
     */
    private $totalRegistry;

    public function __construct(
        State $appState,
        StoreManagerInterface $storeManager,
        Validator $validator,
        HashProvider $hashProvider,
        RuleRepositoryInterface $ruleRepository,
        CustomerGroupProvider $groupProvider,
        ItemsProvider $itemsProvider,
        TotalRegistry $totalRegistry
    ) {
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->validator = $validator;
        $this->hashProvider = $hashProvider;
        $this->ruleRepository = $ruleRepository;
        $this->groupProvider = $groupProvider;
        $this->itemsProvider = $itemsProvider;
        $this->totalRegistry = $totalRegistry;
    }

    /**
     * reset storage
     */
    public function reset(): void
    {
        $this->storage = [];
        $this->itemsProvider->reset();
    }

    /**
     * @param RateRequest $request
     * @return array
     */
    public function getValidRules(RateRequest $request): array
    {
        $allItems = $request->getAllItems();

        if (empty($allItems)) {
            return [];
        }

        $hash = $this->hashProvider->getHash($request);

        if (isset($this->storage[$hash])) {
            return $this->storage[$hash];
        }

        $this->storage[$hash] = [];

        foreach ($this->getAllRules($request) as $rule) {
            $validItems = $this->itemsProvider->getValidItems($rule, $allItems);

            if (empty($validItems)) {
                continue;
            }

            $newItems = [];
            foreach ($request->getAllItems() as $item) {
                $itemId = (int)($item->getId() ?? $item->getQuoteItemId());
                if (isset($validItems[$itemId])) {
                    $newItems[] = $validItems[$itemId];
                }
            }
            $newRequest = clone $request;
            $newRequest->setAllItems(array_values($newItems));
            $newHash = $this->hashProvider->getHash($newRequest);
            $total = $this->totalRegistry->getCalculatedTotal($newRequest, $newHash);

            if ($this->validator->validateRule($rule, $request, $total)) {
                $this->storage[$hash][] = $rule;

                if ($rule->getSkipSubsequent()) {
                    break;
                }
            }
        }

        return $this->storage[$hash];
    }

    /**
     * @param RateRequest $rateRequest
     * @return RuleInterface[]|Rule[]
     */
    private function getAllRules(RateRequest $rateRequest): array
    {
        $isAdmin = $this->appState->getAreaCode() === FrontNameResolver::AREA_CODE;
        $customerGroupId = $this->groupProvider->getCustomerGroupId($rateRequest);

        return $this->ruleRepository->getRulesByParams(
            $this->storeManager->getStore()->getId(),
            $customerGroupId,
            $isAdmin
        );
    }
}
