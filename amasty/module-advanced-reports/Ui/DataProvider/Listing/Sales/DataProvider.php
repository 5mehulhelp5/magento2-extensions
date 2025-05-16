<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\DataProvider\Listing\Sales;

use Amasty\Reports\Model\ResourceModel\Sales\Overview\Grid\Collection;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    public function getData(): array
    {
        $result = parent::getData();
        /** @var Collection $collection */
        $collection = $this->getSearchResult();
        $result['totals'] = $collection->getTotals();
        $this->addPercentColumns($result);

        return $result;
    }

    private function addPercentColumns(array &$result): void
    {
        $fields = [
            'total_orders', 'total_items', 'subtotal', 'tax', 'shipping', 'discounts', 'total', 'invoiced', 'refunded'
        ];

        foreach ($result['items'] as &$item) {
            foreach ($fields as $field) {
                if (!isset($item[$field])) {
                    continue;
                }

                $item['percent_' . $field] = $this->getPercent($item[$field], $result['totals'][$field]);
                $result['totals']['percent_' . $field] = (int) $result['totals'][$field] ? '100%' : __('N/A');
            }
        }
    }

    /**
     * @param $rowValue
     * @param $total
     * @return string
     */
    private function getPercent($rowValue, $total)
    {
        return $total != 0 ? round($rowValue / $total * 100, 2) . '%' : 0;
    }

    /**
     * @inheritdoc
     */
    public function addOrder($field, $direction)
    {
        $field = preg_replace(
            '/^percent_*/',
            '',
            $field
        );
        parent::addOrder($field, $direction);
    }
}
