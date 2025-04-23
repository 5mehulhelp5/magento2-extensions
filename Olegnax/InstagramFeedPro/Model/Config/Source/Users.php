<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\Collection;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\CollectionFactory;

class Users implements ArrayInterface
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Users constructor.
     * @param CollectionFactory $collection
     */
    public function __construct(
        CollectionFactory $collection
    ) {
        $this->collection = $collection->create()
            ->addFieldToSelect('*')
            ->setOrder('username', Collection::SORT_ORDER_ASC);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (empty($this->options)) {
            $this->options = [];
            foreach ($this->collection as $item) {
                $this->options[] = [
                    'value' => $item->getUserId(),
                    'label' => $item->getUsername(),
                    'id' => $item->getIntsuserId(),
                ];
            }
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toIdArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['id'];
        }
        return $array;
    }
}
