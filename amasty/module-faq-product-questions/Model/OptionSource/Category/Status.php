<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\OptionSource\Category;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 1;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_DISABLED, 'label'=> __('Disabled')],
            ['value' => self::STATUS_ENABLED, 'label'=> __('Enabled')]
        ];
    }
}
