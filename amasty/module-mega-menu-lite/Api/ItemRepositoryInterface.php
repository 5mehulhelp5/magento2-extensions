<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Api;

/**
 * @api
 */
interface ItemRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface $item
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface
     */
    public function save(\Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface $item);

    /**
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface
     */
    public function getNew(): \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface
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
     * @return Data\Menu\ItemInterface
     */
    public function getByEntityId($entityId, $storeId, $type);

    /**
     * Delete
     *
     * @param \Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface $item
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface $item);

    /**
     * Delete by id
     *
     * @param int $entityId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
