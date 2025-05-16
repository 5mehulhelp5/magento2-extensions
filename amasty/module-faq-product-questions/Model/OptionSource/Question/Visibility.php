<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Option\ArrayInterface;

class Visibility implements ArrayInterface
{
    public const VISIBILITY_NONE = 0;
    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_FOR_LOGGED = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::VISIBILITY_NONE, 'label'=> __('None')],
            ['value' => self::VISIBILITY_PUBLIC, 'label'=> __('Public')],
            ['value' => self::VISIBILITY_FOR_LOGGED, 'label'=> __('For logged in only')]
        ];
    }
}
