<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Controller\Adminhtml\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;

class MassCancel extends \Amasty\AdvancedReview\Controller\Adminhtml\AbstractMassAction
{
    /**
     * @param ReminderInterface $reminder
     */
    protected function itemAction(ReminderInterface $reminder)
    {
        $reminder->setStatus(Status::CANCELED);
        $this->repository->save($reminder);
    }
}
