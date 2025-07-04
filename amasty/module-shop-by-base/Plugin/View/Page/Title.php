<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Plugin\View\Page;

/**
 * Save category meta title (!without prefixes and suffixes) for further use in customizers.
 */
class Title
{
    public const PAGE_META_TITLE_MAIN_PART = 'am_meta_title_main_part';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Framework\View\Page\Title $subject
     * @param string $title
     */
    public function beforeSet(\Magento\Framework\View\Page\Title $subject, $title)
    {
        $this->registry->register(self::PAGE_META_TITLE_MAIN_PART, $title, true);
    }

    /**
     * @param \Magento\Framework\View\Page\Title $subject
     */
    public function beforeUnsetValue(\Magento\Framework\View\Page\Title $subject)
    {
        $this->registry->register(self::PAGE_META_TITLE_MAIN_PART, '', true);
    }
}
