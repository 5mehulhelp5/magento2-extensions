<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'hotspot_id';

    /**
     * Add collection filters by identifiers
     *
     * @param mixed $productId
     * @param boolean $exclude
     * @return Collection
     */
    public function addIdFilter($productId, $exclude = false)
    {
        if (empty($productId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($productId)) {
            if (!empty($productId)) {
                if ($exclude) {
                    $condition = ['nin' => $productId];
                } else {
                    $condition = ['in' => $productId];
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = ['neq' => $productId];
            } else {
                $condition = $productId;
            }
        }
        $this->addFieldToFilter('hotspot_id', $condition);
        return $this;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Olegnax\InstagramFeedPro\Model\HotSpot::class,
            HotSpot::class
        );
    }
}

