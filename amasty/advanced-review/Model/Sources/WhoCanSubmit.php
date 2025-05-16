<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Data\OptionSourceInterface;

class WhoCanSubmit implements OptionSourceInterface
{
    public const ALL = 'all';
    public const REGISTERED = 'registered';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ALL,
                'label' => __('All')
            ],
            [
                'value' => self::REGISTERED,
                'label' => __('Registered Customers')
            ]
        ];
    }
}
