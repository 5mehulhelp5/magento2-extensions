<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Import\Category\Behaviors;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Api\ImportExport\CategoryInterface;
use Amasty\Faq\Model\CategoryFactory;
use Amasty\Faq\Model\ResourceModel\Category\InsertDummyCategory;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class AddUpdate extends AbstractBehavior
{
    /**
     * @var \Amasty\Faq\Model\Import\Category\Behaviors\Add
     */
    private $addCategory;

    public function __construct(
        Add $addCategory,
        CategoryRepositoryInterface $repository,
        CategoryFactory $categoryFactory,
        CollectionFactory $questionCollectionFactory,
        InsertDummyCategory $dummyCategory,
        StoreManagerInterface $storeManager,
        DataObjectFactory $dataObjectFactory = null // TODO move to not optional
    ) {
        parent::__construct(
            $repository,
            $categoryFactory,
            $questionCollectionFactory,
            $dummyCategory,
            $storeManager,
            $dataObjectFactory
        );
        $this->addCategory = $addCategory;
    }

    public function execute(array $importData): DataObject
    {
        $this->setStores();
        $categoriesToCreate = [];
        $result = $this->dataObjectFactory->create();
        foreach ($importData as $categoryData) {
            $category = null;
            $categoryData[CategoryInterface::CATEGORY_ID] = (int)$categoryData[CategoryInterface::CATEGORY_ID];
            if (!empty($categoryData[CategoryInterface::CATEGORY_ID])) {
                try {
                    $category = $this->repository->getById($categoryData[CategoryInterface::CATEGORY_ID]);
                } catch (NoSuchEntityException $e) {
                    $dummyCategory = $this->categoryFactory->create();
                    $dummyCategory->setCategoryId($categoryData[CategoryInterface::CATEGORY_ID]);
                    $this->dummyCategory->save($dummyCategory);
                    try {
                        $category = $this->repository->getById($categoryData[CategoryInterface::CATEGORY_ID]);
                        $result->setCountItemsCreated((int)$result->getCountItemsCreated() + 1);
                    } catch (NoSuchEntityException $e) {
                        null;
                    }
                }

                if ($category) {
                    $this->setCategoryData($category, $categoryData);
                    try {
                        $this->repository->save($category);
                        if (!isset($dummyCategory)) {
                            $result->setCountItemsUpdated((int)$result->getCountItemsUpdated() + 1);
                        }
                    } catch (CouldNotSaveException $e) {
                        null;
                    }
                }

                unset($dummyCategory);
            } else {
                $categoriesToCreate[] = $categoryData;
            }
        }

        if (!empty($categoriesToCreate)) {
            $addCategoryResult = $this->addCategory->execute($categoriesToCreate);
            $result->setCountItemsCreated(
                (int)$addCategoryResult->getCountItemsCreated() + (int)$result->getCountItemsCreated()
            );
        }

        return $result;
    }
}
