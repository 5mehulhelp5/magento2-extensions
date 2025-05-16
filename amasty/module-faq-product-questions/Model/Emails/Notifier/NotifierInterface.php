<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Emails\Notifier;

interface NotifierInterface
{
    /**
     * Sends email
     *
     * @param \Amasty\Faq\Api\Data\QuestionInterface $question
     */
    public function notify(\Amasty\Faq\Api\Data\QuestionInterface $question): void;
}
