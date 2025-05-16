<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\ResourceModel\Question;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ResourceModel\Question as QuestionResource;

class InsertDummyQuestion extends \Amasty\Faq\Model\ResourceModel\AbstractDummy
{
    public function _construct()
    {
        $this->_init(QuestionResource::TABLE_NAME, QuestionInterface::QUESTION_ID);
    }
}
