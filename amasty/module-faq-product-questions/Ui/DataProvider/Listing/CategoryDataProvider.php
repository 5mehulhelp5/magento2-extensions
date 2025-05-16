<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Ui\DataProvider\Listing;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Model\ResourceModel\Category\Collection;
use Magento\Framework\Api\Filter;
use Magento\Store\Model\Store;

class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        CategoryRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();

        foreach ($data['items'] as $key => $category) {
            $categoryData = $this->repository->getById($category['category_id'])->getData();
            $data['items'][$key] = $categoryData;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() === Store::STORE_ID) {
            /** @var Collection $collection */
            $collection = $this->getCollection();
            $collection->addStoreFilter([Store::DEFAULT_STORE_ID, $filter->getValue()]);
        } else {
            parent::addFilter($filter);
        }
    }
}
