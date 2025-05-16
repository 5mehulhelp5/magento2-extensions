<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Ui\DataProvider\Listing;

use Amasty\AdvancedReview\Ui\DataProvider\Listing;
use Magento\Framework\Api\Search\SearchResultInterface;

class CommentDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];

        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $data = $item->getData();
            if (isset($data['stores']) && $data['stores'] == '0') {
                $data['stores'] = ['0'];
            }

            if (isset($data['store_id']) && is_string($data['store_id'])) {
                $data['store_id'] = explode(',', $data['store_id']);
            }
            $arrItems['items'][] = $data;
        }

        $arrItems['totalRecords'] = $searchResult->getSize();

        return $arrItems;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter): void
    {
        if ($filter->getField() == 'store_id') {
            $filter->setValue([$filter->getValue(), \Magento\Store\Model\Store::DEFAULT_STORE_ID]);
            $filter->setConditionType('in');
        }
        parent::addFilter($filter);
    }
}
