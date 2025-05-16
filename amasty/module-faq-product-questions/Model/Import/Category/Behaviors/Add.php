<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Import\Category\Behaviors;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotSaveException;

class Add extends AbstractBehavior
{
    public function execute(array $importData): DataObject
    {
        $this->setStores();
        $result = $this->dataObjectFactory->create();
        foreach ($importData as $categoryData) {
            $category = $this->categoryFactory->create();
            $this->setCategoryData($category, $categoryData);
            try {
                $this->repository->save($category);
                $result->setCountItemsCreated((int)$result->getCountItemsCreated() + 1);
            } catch (CouldNotSaveException $e) {
                null;
            }
        }

        return $result;
    }
}
