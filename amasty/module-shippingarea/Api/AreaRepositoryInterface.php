<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Api;

/**
 * @api
 */
interface AreaRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ShippingArea\Api\Data\AreaInterface $area
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\ShippingArea\Api\Data\AreaInterface $area);

    /**
     * Get by id
     *
     * @param int $areaId
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($areaId);

    /**
     * Delete
     *
     * @param \Amasty\ShippingArea\Api\Data\AreaInterface $area
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ShippingArea\Api\Data\AreaInterface $area);

    /**
     * Delete by id
     *
     * @param int $areaId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($areaId);
}
