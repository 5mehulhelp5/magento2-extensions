<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\Sitemap\ItemProvider;

use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magefan\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magefan\Blog\Api\SitemapConfigInterface;

class Tag implements ItemProviderInterface
{
    /**
     * Sitemap config
     *
     * @var SitemapConfigInterface
     */
    private $sitemapConfig;

    /**
     * Blog tag collection factory
     *
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Sitemap item factory
     *
     * @var SitemapItemInterfaceFactory
     */
    private $itemFactory;

    /**
     * @param SitemapConfigInterface $sitemapConfig
     * @param CollectionFactory $collectionFactory
     * @param SitemapItemInterfaceFactory $itemFactory
     */
    public function __construct(
        SitemapConfigInterface $sitemapConfig,
        CollectionFactory $collectionFactory,
        SitemapItemInterfaceFactory $itemFactory
    ) {
        $this->sitemapConfig = $sitemapConfig;
        $this->collectionFactory = $collectionFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($storeId)
    {
        if (!$this->sitemapConfig->isEnabledSitemap(SitemapConfigInterface::TAGS_PAGE, $storeId)) {
            return [];
        }

        $collection = $this->collectionFactory->create()
            ->addStoreFilter($storeId)
            ->addActiveFilter()
            ->getItems();

        $items = array_map(function ($item) use ($storeId) {
            return $this->itemFactory->create([
                'url' => $item->getUrl(),
                'updatedAt' => $item->getUpdatedAt(),
                'priority' => $this->sitemapConfig->getPriority(SitemapConfigInterface::TAGS_PAGE, $storeId),
                'changeFrequency' => $this->sitemapConfig->getFrequency(SitemapConfigInterface::TAGS_PAGE, $storeId),
            ]);
        }, $collection);

        return $items;
    }
}
