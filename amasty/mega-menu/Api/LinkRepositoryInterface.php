<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Api;

use Amasty\MegaMenuLite\Api\LinkRepositoryInterface as LinkRepositoryInterfaceLite;

/**
 * @api
 */
interface LinkRepositoryInterface extends LinkRepositoryInterfaceLite
{
    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkSearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
