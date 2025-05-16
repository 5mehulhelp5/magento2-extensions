<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Page for Magento 2 (System)
 */

namespace Amasty\ShopbyPage\Plugin\ShopbySeo\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Amasty\ShopbyPage\Model\Page;

class Meta
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Amasty\ShopbySeo\Helper\Meta $subject
     * @param \Closure $proceed
     * @param bool $indexTag
     * @param DataObject $data
     * @return bool
     */
    public function aroundGetIndexTagByData(
        \Amasty\ShopbySeo\Helper\Meta $subject,
        \Closure $proceed,
        $indexTag,
        DataObject $data
    ) {
        $keepIndex = $this->registry->registry(Page::MATCHED_PAGE);
        return $keepIndex ? $indexTag : $proceed($indexTag, $data);
    }
}
