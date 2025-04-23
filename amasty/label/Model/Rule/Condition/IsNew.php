<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Rule\Condition;

use Amasty\Label\Model\ConfigProvider;
use Amasty\Label\Model\ScopeDateValidator;
use Amasty\Label\Model\Source\Rules\Operator\BooleanOptions as IsNewOptionSource;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

class IsNew extends AbstractCondition
{
    /**
     * @var Yesno
     */
    private $yesNoOptionProvider;

    /**
     * @var IsNewOptionSource
     */
    private $isNewOperatorProvider;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ScopeDateValidator
     */
    private $scopeDateValidator;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        Context $context,
        Yesno $yesNoOptionProvider,
        ConfigProvider $configProvider,
        IsNewOptionSource $isNewOperatorProvider,
        TimezoneInterface $timezone,
        ScopeDateValidator $scopeDateValidator,
        array $data = [],
        ExpressionFactory $expressionFactory = null, // TODO move to not optional
        DateTime $dateTime = null // TODO move to not optional
    ) {
        $this->yesNoOptionProvider = $yesNoOptionProvider;
        $this->isNewOperatorProvider = $isNewOperatorProvider;
        $this->configProvider = $configProvider;
        $this->timezone = $timezone;
        $this->scopeDateValidator = $scopeDateValidator;
        $this->expressionFactory = $expressionFactory ?: ObjectManager::getInstance()->get(ExpressionFactory::class);
        $this->dateTime = $dateTime ?: ObjectManager::getInstance()->get(DateTime::class);

        parent::__construct(
            $context,
            $data
        );
    }

    public function collectValidatedAttributes(ProductCollection $collection): void
    {
        if ($this->configProvider->useNewFromToRanges()) {
            $collection->addAttributeToSelect('news_from_date', 'left');
            $collection->addAttributeToSelect('news_to_date', 'left');
        } elseif ($this->configProvider->useCreationDateForNew()) {
            $collection->addAttributeToSelect(ProductInterface::CREATED_AT);
        }
    }

    /**
     * @return string|void
     */
    public function getMappedSqlField()
    {
        if ($this->configProvider->useNewFromToRanges()) {
            $exprArray = [
                '(%2$s IS NOT NULL OR %3$s IS NOT NULL)', // one of dates must be not null
                '(%2$s IS NULL OR \'%1$s\' >= %2$s)', // check from date
                '(%3$s IS NULL OR \'%1$s\' <= %3$s)' // check to date
            ];
            return $this->expressionFactory->create([
                'expression' => sprintf(
                    'IF(' . implode(' AND ', $exprArray) . ', 1, 0)',
                    $this->getCurrentDate(),
                    'IFNULL(at_news_from_date.value, at_news_from_date_default.value)',
                    'IFNULL(at_news_to_date.value, at_news_to_date_default.value)'
                )
            ]);
        } elseif ($this->configProvider->useCreationDateForNew()) {
            return $this->expressionFactory->create([
                'expression' => sprintf(
                    'IF(TIMESTAMPDIFF(DAY, e.created_at, \'%s\') < %s, 1, 0)',
                    $this->getCurrentDate(),
                    $this->configProvider->getIsNewDaysThreshold()
                )
            ]);
        }
    }

    /**
     * @return int
     */
    public function getBindArgumentValue()
    {
        return (int)$this->getValue();
    }

    private function getCurrentDate(): string
    {
        return $this->dateTime->gmtDate(Mysql::TIMESTAMP_FORMAT);
    }

    public function validate(AbstractModel $model): bool
    {
        /** @var Product $model **/
        $isProductNew = $this->isProductNew($model);
        $requiredValue = (bool) $this->getValue();

        return $this->getOperator() === '==' ? ($isProductNew === $requiredValue) : ($isProductNew !== $requiredValue);
    }

    private function isProductNew(Product $product): bool
    {
        $isNew = false;
        $useFromToRanges = $this->configProvider->useNewFromToRanges();
        $fromDate = $product->getNewsFromDate();
        $toDate = $product->getNewsToDate();

        if ($useFromToRanges && ($fromDate !== null || $toDate !== null)) {
            $isNew = $this->isNewUsingRanges($product);
        } elseif ($this->configProvider->useCreationDateForNew()) {
            $isNew = $this->isNewUsingCreatedAt($product);
        }

        return $isNew;
    }

    private function isNewUsingRanges(Product $product): bool
    {
        $fromDate = $product->getNewsFromDate();
        $toDate = $product->getNewsToDate();

        return $this->scopeDateValidator->isScopeDateInInterval(
            (int)$product->getStoreId(),
            $fromDate,
            $toDate
        );
    }

    private function isNewUsingCreatedAt(Product $product): bool
    {
        $result = false;
        $createdAtDate = $product->getCreatedAt();

        if ($createdAtDate !== null) {
            $createdAtDate = $this->timezone->scopeDate($product->getStore(), $createdAtDate, true);
            $now = $this->timezone->date();
            $dateDiff = $now->diff($createdAtDate);
            $result = $dateDiff->days < $this->configProvider->getIsNewDaysThreshold();
        }

        return $result;
    }

    public function getAttributeElementHtml(): Phrase
    {
        return __('Is New');
    }

    public function getInputType(): string
    {
        return 'select';
    }

    public function getValueElementType(): string
    {
        return 'select';
    }

    public function getOperatorSelectOptions(): array
    {
        return $this->isNewOperatorProvider->toOptionArray();
    }

    public function getValueSelectOptions(): array
    {
        return $this->yesNoOptionProvider->toOptionArray();
    }
}
