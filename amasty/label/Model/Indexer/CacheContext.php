<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Indexer;

class CacheContext extends \Magento\Framework\Indexer\CacheContext
{
    /**
     * @var array
     */
    private $tags = [];

    /**
     * Register entity Ids
     *
     * @param string $cacheTag
     * @param array $ids
     * @return $this
     */
    public function registerEntities($cacheTag, $ids)
    {
        $this->entities[$cacheTag] = $ids;

        return $this;
    }

    /**
     * Clear context data
     */
    public function flush(): void
    {
        $this->tags = [];
        $this->entities = [];
    }
}
