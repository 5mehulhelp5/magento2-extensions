<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Email;

use Amasty\Base\Model\Serializer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\Converter\ToDataModel;
use Magento\SalesRule\Model\Converter\ToModel;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

class SalesRuleProvider
{

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CouponDataProvider
     */
    private $couponDataProvider;

    /**
     * @var CouponConditionsProvider
     */
    private $couponConditionsProvider;

    /**
     * @var ToDataModel
     */
    private $toDataModelConverter;

    /**
     * @var ToModel
     */
    private $toModelConverter;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * SalesRuleProvider constructor.
     * @param RuleFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param StoreManagerInterface $storeManager
     * @param CouponDataProvider $couponDataProvider
     * @param CouponConditionsProvider $couponConditionsProvider
     * @param ToDataModel $toDataModelConverter
     * @param ToModel $toModelConverter
     * @param Serializer $serializer
     * @param Flag $flag
     * @param DateTime $date
     */
    public function __construct(
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        StoreManagerInterface $storeManager,
        CouponDataProvider $couponDataProvider,
        CouponConditionsProvider $couponConditionsProvider,
        ToDataModel $toDataModelConverter,
        ToModel $toModelConverter,
        Serializer $serializer,
        Flag $flag,
        DateTime $date
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->storeManager = $storeManager;
        $this->couponDataProvider = $couponDataProvider;
        $this->couponConditionsProvider = $couponConditionsProvider;
        $this->toDataModelConverter = $toDataModelConverter;
        $this->toModelConverter = $toModelConverter;
        $this->serializer = $serializer;
        $this->flag = $flag;
        $this->date = $date;
    }

    /**
     * @param WebsiteInterface $website
     * @return Rule
     */
    public function getRule(WebsiteInterface $website): Rule
    {
        try {
            $rule = $this->ruleRepository->getById($this->flag->getRuleIdByWebsiteId((int)$website->getId()));
            $rule = $this->toModelConverter->toModel($rule);
            $now = $this->date->date('Y-m-d');

            if (!$rule->getIsActive() || !($rule->getFromDate() <= $now && $rule->getToDate() >= $now)) {
                $rule = $this->ruleFactory->create();
            }
        } catch (NoSuchEntityException $exception) {
            $rule = $this->ruleFactory->create();
        }

        $rule = $this->buildRule($rule, $website);
        $this->flag->addRuleIdByWebsite((int)$rule->getRuleId(), (int)$website->getId());

        return $this->toModelConverter->toModel($rule);
    }

    /**
     * @param int|null $websiteId
     */
    public function initWebsiteRule(?int $websiteId = null): void
    {
        if ($websiteId) {
            $rule = $this->buildRule($this->ruleFactory->create(), $this->storeManager->getWebsite($websiteId));
            $this->flag->addRuleIdByWebsite((int)$rule->getRuleId(), $websiteId);
            return;
        }

        foreach ($this->storeManager->getWebsites() as $website) {
            $rule = $this->ruleFactory->create();
            $rule = $this->buildRule($rule, $website);
            $flagData[$website->getId()] = $rule->getRuleId();
        }

        $this->flag->addFlagData($flagData ?? []);
    }

    /**
     * @param Rule $rule
     * @param WebsiteInterface $website
     * @return RuleInterface
     */
    private function buildRule(Rule $rule, WebsiteInterface $website): RuleInterface
    {
        $rule->loadPost(array_merge(
            $this->couponDataProvider->generateCouponData($website),
            $this->couponConditionsProvider->generateConditions()
        ));
        $this->convertDateTimeIntoDates($rule);
        $rule->setConditionsSerialized($this->serializer->serialize($rule->getConditions()->asArray()));
        return $this->ruleRepository->save($this->toDataModelConverter->toDataModel($rule));
    }

    /**
     * @param Rule $rule
     * @return void
     */
    private function convertDateTimeIntoDates(Rule $rule): void
    {
        foreach (['from_date', 'to_date'] as $ruleAttribute) {
            if ($ruleAttributeValue = $rule->getData($ruleAttribute)) {
                if ($ruleAttributeValue instanceof \DateTime) {
                    $ruleAttributeValue = $ruleAttributeValue->format('Y-m-d');
                    $rule->setData($ruleAttribute, $ruleAttributeValue);
                }
            }
        }
    }
}
