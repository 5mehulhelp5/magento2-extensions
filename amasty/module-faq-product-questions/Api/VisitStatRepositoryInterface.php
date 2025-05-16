<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api;

/**
 * @api
 */
interface VisitStatRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Faq\Api\Data\VisitStatInterface $visitStat
     * @return \Amasty\Faq\Api\Data\VisitStatInterface
     */
    public function save(\Amasty\Faq\Api\Data\VisitStatInterface $visitStat);
    /**
     * Get by id
     *
     * @param int $tagId
     * @return \Amasty\Faq\Api\Data\VisitStatInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($tagId);

    /**
     * Delete all records
     *
     * @return bool
     */
    public function deleteAll();
}
