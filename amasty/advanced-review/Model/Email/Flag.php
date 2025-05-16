<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Email;

use Magento\Framework\FlagFactory;
use Magento\Framework\Flag as BaseFlag;
use Magento\Framework\Flag\FlagResource;

class Flag
{

    public const FLAG_CODE = 'amasty_advanced_review_sales_rule_ids';

    /**
     * @var BaseFlag
     */
    private $flagFactory;

    /**
     * @var FlagResource
     */
    private $flagResource;

    /**
     * @var BaseFlag
     */
    private $flag;

    /**
     * Flag constructor.
     * @param FlagFactory $flagFactory
     * @param FlagResource $flagResource
     */
    public function __construct(
        FlagFactory $flagFactory,
        FlagResource $flagResource
    ) {
        $this->flagFactory = $flagFactory;
        $this->flagResource = $flagResource;
    }

    /**
     * @param int $websiteId
     * @return int|null
     */
    public function getRuleIdByWebsiteId(int $websiteId): ?int
    {
        $flagData = $this->getFlag()->getFlagData();
        return isset($flagData[$websiteId]) ? (int) $flagData[$websiteId] : null;
    }

    /**
     * @param int $ruleId
     * @param int $websiteId
     */
    public function addRuleIdByWebsite(int $ruleId, int $websiteId): void
    {
        $flagData = $this->getFlag()->getFlagData();
        $flagData[$websiteId] = $ruleId;
        $this->flag->setFlagData($flagData);
        $this->saveFlag();
    }

    /**
     * @param array $data
     */
    public function addFlagData(array $data): void
    {
        $this->getFlag()->setFlagData($data);
        $this->saveFlag();
    }

    /**
     * @return void
     */
    public function saveFlag(): void
    {
        $this->flagResource->save($this->flag);
    }

    /**
     * @return BaseFlag
     */
    private function getFlag(): BaseFlag
    {
        if (!$this->flag) {
            $this->flag = $this->flagFactory->create(['data' => ['flag_code' => self::FLAG_CODE]]);
        }

        return $this->flag->loadSelf();
    }
}
