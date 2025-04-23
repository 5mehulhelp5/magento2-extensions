<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserSearchResultsInterface;

interface IntsUserRepositoryInterface
{

    /**
     * Save IntsUser
     * @param IntsUserInterface $intsUser
     * @return IntsUserInterface
     * @throws LocalizedException
     */
    public function save(
        IntsUserInterface $intsUser
    );

    /**
     * Retrieve IntsUser
     * @param string $intsuserId
     * @return IntsUserInterface
     * @throws LocalizedException
     */
    public function get($intsuserId);

    /**
     * Retrieve IntsUser matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return IntsUserSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete IntsUser
     * @param IntsUserInterface $intsUser
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        IntsUserInterface $intsUser
    );

    /**
     * Delete IntsUser by ID
     * @param string $intsuserId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($intsuserId);
}
