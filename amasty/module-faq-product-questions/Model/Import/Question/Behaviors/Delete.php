<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Import\Question\Behaviors;

use Amasty\Faq\Api\ImportExport\QuestionInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotDeleteException;

class Delete extends AbstractBehavior
{
    public function execute(array $importData): DataObject
    {
        $result = $this->dataObjectFactory->create();
        foreach ($importData as $questionData) {
            if (!empty($questionData[QuestionInterface::QUESTION_ID])) {
                try {
                    $this->repository->deleteById((int)$questionData[QuestionInterface::QUESTION_ID]);
                    $result->setCountItemsDeleted((int)$result->getCountItemsDeleted() + 1);
                } catch (CouldNotDeleteException $e) {
                    null;
                }
            }
        }

        return $result;
    }
}
