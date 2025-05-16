<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\OptionSource\Widget;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    public const RANDOM = 0;
    public const RECENT = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::RECENT, 'label'=> __('Recent')],
            ['value' => self::RANDOM, 'label'=> __('Random')]
        ];
    }
}
