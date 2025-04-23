<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\IntsPost;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Olegnax\InstagramFeedPro\Model\HotSpot;
use Olegnax\InstagramFeedPro\Model\IntsPost;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory;

class DataProvider extends AbstractDataProvider
{

    protected $collection;

    protected $dataPersistor;

    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     * @noinspection PhpDeprecationInspection
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var IntsPost $model */
        foreach ($items as $model) {
            $model = $model->load($model->getId()); //temporary fix
            $data = $model->getData();

            $data['data'] = ['links' => []];

            /* Prepare related products */
            $collection = $model->getCollectionRelatedProducts(true)->addAttributeToSelect('name');
            $items = [];
            /** @var Product $item */
            foreach ($collection as $item) {
                $_item = [];
                foreach (['name', 'store_id'] as $field) {
                    $_item[$field] = $item->getData($field);
                }
                if (!empty($_item)) {
                    $items[$item->getId()] = $_item;
                }
            }
            $related = [];
            foreach ($data['related'] as $item) {
                if (array_key_exists($item['entity_id'], $items)) {
                    $related[] = array_replace($items[$item['entity_id']], $item);
                }
            }
            $data['data']['links']['product'] = $related;
            /* Prepare related products */
            /* Prepare hot spot products */
            $collection = $model->getCollectionRelatedHotSpots()->addFieldToSelect('*');
            $items = [];
            /** @var HotSpot $item */
            foreach ($collection as $item) {
                $items[$item->getId()] = $item->getData();
            }
            $hotSpots = [];
            foreach ($data['hotspot'] as $item) {
                if (array_key_exists($item['hotspot_id'], $items)) {
                    $hotSpots[] = array_replace($items[$item['hotspot_id']], $item);
                }
            }
            $data['data']['links']['hotspot'] = $hotSpots;
            /* Prepare hot spot products */

            $this->loadedData[$model->getId()] = $data;
        }
        $data = $this->dataPersistor->get('olegnax_instagramfeedpro_intspost');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('olegnax_instagramfeedpro_intspost');
        }

        return $this->loadedData;
    }
}
