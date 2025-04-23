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
use Olegnax\InstagramFeedPro\Model\HotSpot;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot\Collection;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot\CollectionFactory;

class MarkerStyleWithCustom implements ArrayInterface
{

    /**
     * @var MarkerStyle
     */
    protected $markerStyle;
    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var array
     */
    protected $array;
    /**
     * @var array[]
     */
    protected $options;
    /**
     * @var array
     */
    protected $iconArray;

    /**
     * MarkerStyleWithCustom constructor.
     * @param MarkerStyle $markerStyle
     * @param CollectionFactory $collection
     */
    public function __construct(
        MarkerStyle $markerStyle,
        CollectionFactory $collection

    ) {
        $this->markerStyle = $markerStyle;

        $this->collection = $collection->create()
            ->addFieldToSelect('*')
            ->setOrder('name', Collection::SORT_ORDER_ASC);
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->array)) {
            $this->array = [];
            foreach ($this->toOptionArray() as $item) {
                $this->array[$item['value']] = $item['label'];
            }
        }

        return $this->array;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {

        if (empty($this->options)) {
            $this->options = [
                [
                    'value' => '',
                    'label' => __('None'),
                ],
            ];
            $this->options = array_merge($this->options, $this->markerStyle->toOptionArray());
            /** @var HotSpot $item */
            foreach ($this->collection as $item) {
                $this->options[] = [
                    'value' => $item->getHotspotId(),
                    'label' => $item->getName(),
                    'icon' => [
                        'prepare' => 'prepareTemplateDB',
                        'id' => $item->getHotspotId(),
                    ],

                ];
            }
        }

        return $this->options;
    }

    /**
     * @return array
     */
    public function toIconArray()
    {
        if (empty($this->iconArray)) {
            $this->iconArray = [];
            foreach ($this->toOptionArray() as $item) {
                $this->iconArray[$item['value']] = isset($item['icon']) ? $item['icon'] : '';
            }
        }
        return $this->iconArray;
    }
}
