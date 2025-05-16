<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\OptionSource\Widget\QuestionList;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

class WidgetType implements OptionSourceInterface, ArrayInterface
{
    public const SPECIFIC_CATEGORY = 1;
    public const SPECIFIC_QUESTIONS = 2;
    public const SPECIFIC_PRODUCT = 3;
    public const CURRENT_PRODUCT = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SPECIFIC_CATEGORY, 'label'=> __('FAQ Category')],
            ['value' => self::SPECIFIC_QUESTIONS, 'label'=> __('Specific Questions')],
            ['value' => self::SPECIFIC_PRODUCT, 'label'=> __('From Specific Product')],
            ['value' => self::CURRENT_PRODUCT, 'label'=> __('From Current Product')]
        ];
    }
}
