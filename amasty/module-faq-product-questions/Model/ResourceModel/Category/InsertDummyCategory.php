<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\ResourceModel\Category;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Model\ResourceModel\Category as CategoryResource;

class InsertDummyCategory extends \Amasty\Faq\Model\ResourceModel\AbstractDummy
{
    public function _construct()
    {
        $this->_init(CategoryResource::TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }
}
