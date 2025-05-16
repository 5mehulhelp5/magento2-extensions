<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\ResourceModel\Reminder;

use Amasty\AdvancedReview\Api\Data\ReminderInterface;
use Amasty\AdvancedReview\Model\OptionSource\Reminder\Status;

class ReadyToSendCollection extends Collection
{
    /**
     * @return $this
     */
    public function execute()
    {
        $this->addFieldToFilter(ReminderInterface::STATUS, Status::WAITING);
        $this->getSelect()->where(ReminderInterface::SEND_DATE . '< NOW()');

        return $this;
    }
}
