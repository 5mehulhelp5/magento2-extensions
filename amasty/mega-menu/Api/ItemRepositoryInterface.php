<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Api;

use Amasty\MegaMenuLite\Api\ItemRepositoryInterface as ItemRepositoryInterfaceLite;

/**
 * @api
 */
interface ItemRepositoryInterface extends ItemRepositoryInterfaceLite
{
    /**
     * Save
     *
     * @param \Amasty\MegaMenu\Api\Data\Menu\ItemInterface $item
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function save(\Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface $item);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get by entity id & store id
     *
     * @param int $entityId
     * @param int $storeId
     * @param string $type
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function getByEntityId($entityId, $storeId, $type);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
