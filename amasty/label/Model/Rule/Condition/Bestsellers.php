<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Rule\Condition;

use Amasty\Label\Model\ResourceModel\Sorting\BestSeller;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;

class Bestsellers extends AbstractCondition
{
    public const FLAG = 'amlabel_bestseller';

    public const MAX_INT = (2 << 30) - 1;

    /**
     * @var BestSeller
     */
    private $bestSellerModel;

    /**
     * @var FormatInterface
     */
    private $format;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    public function __construct(
        Context $context,
        BestSeller $bestSellerModel,
        FormatInterface $format,
        array $data = [],
        ExpressionFactory $expressionFactory = null // TODO move to not optional
    ) {
        parent::__construct($context, $data);
        $this->bestSellerModel = $bestSellerModel;
        $this->format = $format;
        $this->expressionFactory = $expressionFactory ?? ObjectManager::getInstance()->get(ExpressionFactory::class);
    }

    public function collectValidatedAttributes(ProductCollection $collection): void
    {
        if (!$collection->getFlag(self::FLAG)) {
            $collection->setFlag(self::FLAG);
            $select = $this->bestSellerModel->getBestSellerPositionSelect((int) $collection->getStoreId());
            if ($select) {
                $collection->getSelect()->joinLeft(
                    ['position' => $select],
                    'e.entity_id = position.product_id',
                    ['bestseller_position']
                );
            }
        }
    }

    /**
     * @return string
     */
    public function getMappedSqlField()
    {
        return $this->expressionFactory->create(['expression' => sprintf(
            'IF(position.bestseller_position IS NOT NULL AND position.bestseller_position != 0,
            position.bestseller_position,
            %s)',
            self::MAX_INT
        )]);
    }

    /**
     * @return int
     */
    public function getBindArgumentValue()
    {
        return (int)$this->getValue();
    }

    public function validate(AbstractModel $model)
    {
        $value = $model->getData($this->getAttribute());
        if (!$value) {
            $model->setData($this->getAttribute(), self::MAX_INT);
        }

        return parent::validate($model);
    }

    public function getAttribute(): string
    {
        return 'bestseller_position';
    }

    public function getAttributeElementHtml(): Phrase
    {
        return __('Bestseller Position');
    }

    public function getInputType(): string
    {
        return 'numeric';
    }

    public function getValueElementType(): string
    {
        return 'text';
    }

    public function getDefaultOperatorOptions(): array
    {
        $values = parent::getDefaultOperatorOptions();
        unset($values['{}']);
        unset($values['!{}']);
        unset($values['<=>']);

        return $values;
    }

    /**
     * Load condition from array.
     *
     * @param array $arr
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function loadArray($arr)
    {
        $tmp = [];
        foreach (explode(',', ($arr['value'] ?? '')) as $value) {
            $tmp[] = $this->format->getNumber($value);
        }
        $arr['value'] = implode(',', $tmp);

        return parent::loadArray($arr);
    }
}
