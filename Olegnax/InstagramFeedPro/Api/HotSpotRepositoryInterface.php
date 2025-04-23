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
use Olegnax\InstagramFeedPro\Api\Data\HotSpotInterface;
use Olegnax\InstagramFeedPro\Api\Data\HotSpotSearchResultsInterface;

interface HotSpotRepositoryInterface
{

    /**
     * Save HotSpot
     * @param HotSpotInterface $hotSpot
     * @return HotSpotInterface
     * @throws LocalizedException
     */
    public function save(
        HotSpotInterface $hotSpot
    );

    /**
     * Retrieve HotSpot
     * @param string $hotspotId
     * @return HotSpotInterface
     * @throws LocalizedException
     */
    public function get($hotspotId);

    /**
     * Retrieve HotSpot matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return HotSpotSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete HotSpot
     * @param HotSpotInterface $hotSpot
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        HotSpotInterface $hotSpot
    );

    /**
     * Delete HotSpot by ID
     * @param string $hotspotId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($hotspotId);
}

