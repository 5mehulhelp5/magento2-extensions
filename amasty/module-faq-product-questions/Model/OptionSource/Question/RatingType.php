<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\OptionSource\Question;

use Magento\Framework\Data\OptionSourceInterface;

class RatingType implements OptionSourceInterface
{
    public const YESNO = 0;
    public const VOTING = 1;
    public const AVERAGE = 2;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::YESNO,
                'label'=> __('Yes/No')
            ],
            [
                'value' => self::VOTING,
                'label'=> __('Voting')
            ],
            [
                'value' => self::AVERAGE,
                'label'=> __('Average Rating')
            ],
        ];
    }
}
