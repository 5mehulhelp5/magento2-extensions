<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Label Cache Cleaner for Qty Variable
 */

namespace Amasty\LabelCacheCleanerForQtyVariable\Model;

use Amasty\Label\Model\Indexer\CacheContext;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Event\ManagerInterface;

class FlushCache
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var CacheContext
     */
    private $cacheContext;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        CacheInterface $cache,
        CacheContext $cacheContext,
        ManagerInterface $eventManager
    ) {
        $this->cache = $cache;
        $this->cacheContext = $cacheContext;
        $this->eventManager = $eventManager;
    }

    /**
     * @param string $cacheTag
     * @param int[] $ids
     */
    public function execute(string $cacheTag, array $ids): void
    {
        $this->cacheContext->registerEntities($cacheTag, $ids);
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);

        $identities = $this->cacheContext->getIdentities();
        if (!empty($identities)) {
            $this->cache->clean($identities);
            $this->cacheContext->flush();
        }
    }
}
