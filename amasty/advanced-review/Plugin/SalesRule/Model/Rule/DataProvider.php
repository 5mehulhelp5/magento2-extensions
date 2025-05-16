<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Plugin\SalesRule\Model\Rule;

use Amasty\AdvancedReview\Model\Email\CouponDataProvider;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\SalesRule\Model\RegistryConstants;
use Magento\SalesRule\Model\Rule\DataProvider as DataProviderOrigin;

class DataProvider
{
    /** @var string[] Fields for disabling */
    public const INFORMATION_FIELDS = [
        'name',
        'website_ids',
        'coupon_type',
        'coupon_code',
        'use_auto_generation',
        'customer_group_ids'
    ];

    /** @var string[] Fields for disabling */
    public const ACTIONS_FIELDS = [
        'simple_action',
        'discount_amount'
    ];

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * DataProvider constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        Context $context
    ) {
        $this->registry = $registry;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * @param DataProviderOrigin $subject
     * @param $result
     * @return mixed
     */
    public function afterGetMeta(DataProviderOrigin $subject, $result)
    {
        $model = $this->registry->registry(RegistryConstants::CURRENT_SALES_RULE);
        if ($model && !($subject instanceof \Magento\SalesRuleStaging\Model\Rule\UpcomingDataProvider)) {
            if (!$this->isAmastyRule((string) $model->getName())) {
                return $result;
            }

            $this->messageManager->addWarningMessage(
                __(
                    'This shopping cart price rule is assigned to the Amasty Review Reminder Discount program. ' .
                    'Rule conditions are configured under Configuration-Stores-Amasty Advanced Product ' .
                    'Reviews-Discount Coupons.'
                )
            );

            foreach (self::INFORMATION_FIELDS as $field) {
                if (isset($result['rule_information']['children'][$field])) {
                    $result['rule_information']['children'][$field]['arguments']['data']['config']['disabled'] = 1;
                }
            }

            foreach (self::ACTIONS_FIELDS as $field) {
                if (isset($result['actions']['children'][$field])) {
                    $result['actions']['children'][$field]['arguments']['data']['config']['disabled'] = 1;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isAmastyRule(string $name): bool
    {
        return stripos($name, CouponDataProvider::$baseRuleName) !== false;
    }
}
