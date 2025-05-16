<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule\Quote\Address;

use Amasty\CommonRules\Model\Rule\Condition\Address;
use Amasty\Shiprules\Model\Rule\Items\Provider;
use Magento\Quote\Model\Quote\Address\RateRequest;

class HashProvider
{
    /**
     * @var Address
     */
    private $addressCondition;

    /**
     * @var Provider
     */
    private $itemsProvider;

    public function __construct(
        Address $addressCondition,
        Provider $itemsProvider
    ) {
        $this->addressCondition = $addressCondition;
        $this->itemsProvider = $itemsProvider;
    }

    /**
     * @param RateRequest $request
     * @return string
     */
    public function getHash(RateRequest $request): string
    {
        $this->addressCondition->loadAttributeOptions();

        $hash = implode('', $this->itemsProvider->getAllItemIds($request->getAllItems()));
        $addressAttributes = $this->addressCondition->getAttributeOption();
        $addressAttributes += [ //Multishipping attr
            'dest_country_id' => 'dest_country_id',
            'dest_region_id' => 'dest_region_id',
            'dest_city' => 'dest_city',
            'dest_postcode' => 'dest_postcode',
        ];

        foreach ($addressAttributes as $code => $label) {
            $hash .= $request->getData($code) . $label;
        }

        return \hash('md5', $hash);
    }
}
