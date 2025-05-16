<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    public const STATUS_PENDING = 0;
    public const STATUS_ANSWERED = 1;

    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label'=> __('Pending')],
            ['value' => self::STATUS_ANSWERED, 'label'=> __('Answered')]
        ];
    }
}
