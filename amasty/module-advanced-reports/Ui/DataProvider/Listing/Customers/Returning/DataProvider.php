<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\DataProvider\Listing\Customers\Returning;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;

class DataProvider extends UiDataProvider
{
    /**
     * @var string[]
     */
    private $customColumns = ['new_customers', 'returning_customers', 'percent'];

    /**
     * @var array
     */
    private $havingColumns = [];

    /**
     * @var array
     */
    private $havingFilters = [];

    /**
     * @var array
     */
    private $modifiers;

    /**
     * @var ?SearchResultInterface
     */
    private $searchResult;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = [],
        array $modifiers = [],
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->modifiers = $modifiers;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (in_array($filter->getField(), $this->customColumns)) {
            $this->prepareHavingColumns();
            $this->havingFilters[] = $filter;
        }
        // @phpstan-ignore-next-line as adding return statement cause of backward compatibility issue
        parent::addFilter($filter);
    }

    /**
     * @inheritdoc
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $operations = [
            'gteq' => '>=',
            'lteq' => '<=',
            'like' => 'like'
        ];

        foreach ($this->havingFilters as $filter) {
            $fieldExpr = $this->havingColumns[$filter->getField()];
            $searchResult->getSelect()->reset(Select::WHERE)->having(
                sprintf('%s %s "%s"', $fieldExpr, $operations[$filter->getConditionType()], $filter->getValue())
            );
        }

        return parent::searchResultToOutput($searchResult);
    }

    private function prepareHavingColumns(): void
    {
        if (!$this->havingColumns) {
            $config = $this->getConfigData();
            if ($config && isset($config['selectProvider'])) {
                $this->havingColumns['new_customers'] = $config['selectProvider']->getNewCustomersQuery();
                $this->havingColumns['returning_customers'] = $config['selectProvider']->getReturningCustomersSelect();
                $this->havingColumns['percent'] = $config['selectProvider']->getPercentSelect();
            }
        }
    }

    public function getData(): array
    {
        $data = parent::getData();

        foreach ($this->modifiers as $modifier) {
            $data = $modifier->modifyData($data, $this->getSearchResult());
        }

        return $data;
    }

    /**
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        if ($this->searchResult === null) {
            $this->searchResult = parent::getSearchResult();
        }

        return $this->searchResult;
    }
}
