<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Api;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;

interface ImageSettingRepositoryInterface
{
    /**
     * @param int $imageSettingId
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $imageSettingId): ImageSettingInterface;

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $imageSetting
     *
     * @return \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $imageSetting): ImageSettingInterface;

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $imageSetting
     *
     * @return bool true on success
     */
    public function delete(\Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $imageSetting): bool;

    /**
     * @param int $imageSettingId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $imageSettingId): bool;
}
