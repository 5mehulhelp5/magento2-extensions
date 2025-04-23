<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\MagentoReviews\Block\Product\View;

use Magento\Framework\App\RequestInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewsCollectionFactory;
use Magento\Framework\Registry;
use Magento\Review\Block\Product\View\ListView as MagentoListViewBlock;
use Magento\Review\Model\ResourceModel\Review\Collection;

/**
 * Fix Magento bug with esi blocks rendering on product view page
 *
 * @ToDo Remove it when Magento fixes this bug
 */
class ListView
{
    public const FPC_ESI_RENDER_CONTROLLER = 'magento_pagecache_block_esi';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ReviewsCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        RequestInterface $request,
        Registry $registry,
        ReviewsCollectionFactory $collectionFactory
    ) {
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->registry = $registry;
    }

    /**
     * Suppress method call on esi rendering
     *
     * @param MagentoListViewBlock $subject
     * @param callable $proceed
     *
     * @return Collection
     */
    public function aroundGetReviewsCollection(MagentoListViewBlock $subject, callable $proceed)
    {
        if ($this->request->getFullActionName() === self::FPC_ESI_RENDER_CONTROLLER
        && $this->registry->registry('product') === null
        && $subject->getProductId() === null
        ) {
            return $this->collectionFactory->create();
        }

        return $proceed();
    }
}
