<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Api;

/**
 * @api
 */
interface LabelRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Label\Api\Data\LabelInterface $label
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function save(\Amasty\Label\Api\Data\LabelInterface $label): \Amasty\Label\Api\Data\LabelInterface;

    /**
     * Get by id
     *
     * @param int|null $id
     * @param int $mode
     * @return \Amasty\Label\Api\Data\LabelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id, ?int $mode = null);

    /**
     * Delete
     *
     * @param \Amasty\Label\Api\Data\LabelInterface $label
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Label\Api\Data\LabelInterface $label): bool;

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id
     *
     * @return void
     */
    public function duplicateLabel(int $id): void;

    /**
     * Lists
     *
     * @param int|null $mode
     * @return \Amasty\Label\Api\Data\LabelInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAll(?int $mode = null): array;

    /**
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function getModelLabel(): \Amasty\Label\Api\Data\LabelInterface;
}
