<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config\Source\Gdpr;

use Magento\Framework\Data\OptionSourceInterface;

class CheckboxLocation implements OptionSourceInterface
{
    public const FAQ_QUESTION_FORM = 'faq_question_form';

    public function toOptionArray(): array
    {
        return [
            [
                'label' => __('FAQ and Product Questions'),
                'value' => [['label' => __('FAQ Ask Question Form'), 'value' => self::FAQ_QUESTION_FORM]]
            ]
        ];
    }
}
