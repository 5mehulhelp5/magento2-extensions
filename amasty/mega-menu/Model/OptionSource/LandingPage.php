<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class LandingPage implements OptionSourceInterface
{
    public const NOT_SELECT = 0;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => self::NOT_SELECT, 'label' => __('Choose an option')]];
        /** @var \Amasty\Xlanding\Model\Page $page */
        foreach ($this->getLandingPages() as $page) {
            $disabled = $page->isActive() ? '' : sprintf(' (%s)', __('Disabled'));
            $options[] = ['value' => $page->getId(), 'label' => $page->getTitle() . $disabled];
        }

        return $options;
    }

    /**
     * @return array|\Amasty\Xlanding\Model\ResourceModel\Page\Collection
     */
    public function getLandingPages()
    {
        return [];
    }
}
