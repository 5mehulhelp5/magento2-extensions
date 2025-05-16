<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Import\Question\Behaviors;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\DataObject;

class Add extends AbstractBehavior
{
    public function execute(array $importData): DataObject
    {
        $this->setStores();
        $result = $this->dataObjectFactory->create();
        foreach ($importData as $questionData) {
            $question = $this->questionFactory->create();
            $this->setQuestionData($question, $questionData);
            try {
                $this->repository->save($question);
                $result->setCountItemsCreated((int)$result->getCountItemsCreated() + 1);
            } catch (CouldNotSaveException $e) {
                null;
            }
        }

        return $result;
    }
}
