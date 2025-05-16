<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Email;

use Amasty\AdvancedReview\Helper\Config;
use Magento\SalesRule\Model\Rule\Condition\Product\Combine as RuleConditionProductCombine;
use Magento\SalesRule\Model\Rule\Condition\Combine as RuleConditionCombine;
use Magento\SalesRule\Model\Rule\Condition\Address;

class CouponConditionsProvider
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param Config    $configHelper
     */
    public function __construct(Config $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @return array
     */
    public function generateConditions(): array
    {
        return [
            'conditions' => [
                1 => [
                    'type'       => RuleConditionCombine::class,
                    'aggregator' => 'all',
                    'value'      => 1,
                    'new_child'  => '',
                    'conditions' => [
                        '1' => [
                            'type'      => Address::class,
                            'attribute' => 'base_subtotal',
                            'operator'  => '>=',
                            'value'     => (float) $this->configHelper->getModuleConfig('coupons/min_order'),
                        ]
                    ]
                ]
            ],
            'actions' => [
                1 => [
                    'type'       => RuleConditionProductCombine::class,
                    'aggregator' => 'all',
                    'value'      => 1,
                    'new_child'  => '',
                ]
            ]
        ];
    }
}
