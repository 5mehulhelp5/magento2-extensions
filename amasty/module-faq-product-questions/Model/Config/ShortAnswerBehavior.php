<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class ShortAnswerBehavior implements ArrayInterface
{
    public const SHOW_SHORT_ANSWER = 0;
    public const SHOW_CUT_FULL_ANSWER = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SHOW_SHORT_ANSWER, 'label' => __('Show Short answer')],
            ['value' => self::SHOW_CUT_FULL_ANSWER, 'label' => __('Show Cut Full answer')],
        ];
    }
}
