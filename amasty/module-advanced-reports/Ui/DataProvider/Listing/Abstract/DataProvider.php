<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\DataProvider\Listing\Abstract;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;

class DataProvider extends UiDataProvider
{
    /**
     * @var array
     */
    private $modifiers;

    /**
     * @var ?SearchResultInterface
     */
    private $searchResult = null;

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
