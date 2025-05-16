<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;

class CommentStatus implements ArrayInterface
{
    public const STATUS_PENDING = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_REJECTED = 3;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_APPROVED, 'label' => __('Approved')],
            ['value' => self::STATUS_REJECTED, 'label' => __('Rejected')]
        ];
    }
}
