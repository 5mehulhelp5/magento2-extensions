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
interface LinkRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface $link
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface
     */
    public function save(\Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface $link);

    /**
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface
     */
    public function getNew(): \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Delete
     *
     * @param \Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface $link
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface $link);

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
     * @return \Amasty\MegaMenuLite\Api\Data\Menu\LinkSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
