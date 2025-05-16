<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */
namespace Amasty\Shopby\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface CmsPageRepositoryInterface
{
    public const TABLE = 'amasty_amshopby_cms_page';

    /**
     * @param int $pageId
     * @return \Amasty\Shopby\Model\Cms\Page
     * @throws NoSuchEntityException
     */
    public function get($pageId);

    /**
     * @param int $pageId
     * @return \Amasty\Shopby\Model\Cms\Page
     * @throws NoSuchEntityException
     */
    public function getByPageId($pageId);

    /**
     * @param \Amasty\Shopby\Model\Cms\Page $page
     * @return \Amasty\Shopby\Model\Cms\Page
     */
    public function save($page);
}
