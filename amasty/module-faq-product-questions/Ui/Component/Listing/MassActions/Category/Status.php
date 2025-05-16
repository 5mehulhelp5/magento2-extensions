<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Ui\Component\Listing\MassActions\Category;

class Status extends \Amasty\Faq\Ui\Component\Listing\MassActions\MassAction
{
    /**
     * {@inheritdoc}
     */
    public function getUrlParams($optionValue)
    {
        return ['status' => $optionValue];
    }
}
