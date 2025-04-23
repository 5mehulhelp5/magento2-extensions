<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\ResourceModel;

use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;
use Olegnax\InstagramFeedPro\Model\Data\IntsPost as IntsPostData;

class IntsPost extends AbstractDb
{
    const IDENTIFIER_FIELD = 'intspost_id';
    const RELATION_TABLE_STORE = 'olegnax_instagramfeedpro_intspost_store';
    const RELATION_FIELD_STORE = 'store_id';
    const RELATION_TABLE_PRODUCT = 'olegnax_instagramfeedpro_intspost_product_entity';
    const RELATION_FIELD_PRODUCT = 'entity_id';
    const RELATION_TABLE_HOTSPOT = 'olegnax_instagramfeedpro_intspost_hotspot';
    const RELATION_FIELD_HOTSPOT = 'hotspot_id';
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var MetadataPool
     */
    protected $metadataPool;
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * IntsPost constructor.
     * @param Context $context
     * @param Json|null $json
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Json $json = null,
        $connectionName = null
    ) {
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $connectionName);
    }

    /**
     * @param AbstractModel $object
     * @return IntsPost
     * @throws Exception
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStores($object);
        $this->saveProducts($object);
        $this->saveHotSpots($object);
        return parent::_afterSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     * @throws Exception
     */
    public function saveStores(AbstractModel $object)
    {
        $id = (int)$object->getData(static::IDENTIFIER_FIELD);

        $connection = $this->getConnection();

        $oldData = $this->lookupStoreIds($id);
        $newData = $object->getData(IntsPostData::STORE_ID);
        if (empty($newData)) {
            $newData = [];
        }
        $oldData = array_map('intval', $oldData);
        $newData = array_map('intval', $newData);
        $newData = array_unique($newData, SORT_NUMERIC);
        if (empty($newData)) {
            $newData = [Store::DEFAULT_STORE_ID];
        }

        $table = $this->getTable(static::RELATION_TABLE_STORE);

        if ($delete = array_diff($oldData, $newData)) {
            $where = [
                static::IDENTIFIER_FIELD . ' = ?' => $id,
                static::RELATION_FIELD_STORE . ' IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        if ($insert = array_diff($newData, $oldData)) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    static::IDENTIFIER_FIELD => $id,
                    static::RELATION_FIELD_STORE => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $object;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return int[]
     * @throws Exception
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable(static::RELATION_TABLE_STORE), static::RELATION_FIELD_STORE)
            ->where(static::IDENTIFIER_FIELD . ' = :' . static::IDENTIFIER_FIELD);
        $data = $connection->fetchCol($select, [static::IDENTIFIER_FIELD => (int)$id]);
        return $data;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     * @throws Exception
     */
    public function saveProducts(AbstractModel $object)
    {
        $id = (int)$object->getData(static::IDENTIFIER_FIELD);

        $connection = $this->getConnection();

        $oldData = $this->lookupProducts($id);
        $newData = $object->getData(IntsPostData::RELATED);
        if (empty($newData)) {
            $newData = [];
        }
        $oldDataIds = $this->__getOnlyIds($oldData, static::RELATION_FIELD_PRODUCT);
        $newDataIds = $this->__getOnlyIds($newData, static::RELATION_FIELD_PRODUCT);

        $table = $this->getTable(static::RELATION_TABLE_PRODUCT);

        $delete = array_diff($oldDataIds, $newDataIds);
        if ($delete) {
            $where = [
                static::IDENTIFIER_FIELD . ' = ?' => $id,
                static::RELATION_FIELD_PRODUCT . ' IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newDataIds, $oldDataIds);
        $data = [];
        foreach ($newData as $item) {
            if (isset($item['id'])) {
                unset($item['id']);
            }
            if (in_array($item[static::RELATION_FIELD_PRODUCT], $insert)) {
                $item[static::IDENTIFIER_FIELD] = $id;
                $data[] = $item;
            } else {
                $connection->update($table, $item, [
                    static::IDENTIFIER_FIELD . ' = ?' => $id,
                    static::RELATION_FIELD_PRODUCT . ' = ?' => $item[static::RELATION_FIELD_PRODUCT],
                ]);
            }
        }
        if ($data) {
            $connection->insertMultiple($table, $data);
        }

        return $object;
    }

    /**
     * @param int $id
     * @return array[]
     */
    public function lookupProducts($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable(static::RELATION_TABLE_PRODUCT), '*')
            ->where(static::IDENTIFIER_FIELD . ' = :' . static::IDENTIFIER_FIELD)
            ->order('position');
        $data = $connection->fetchAll($select, [static::IDENTIFIER_FIELD => (int)$id]);
        $_data = [];
        foreach ($data as $item) {
            $item['id'] = $item[static::RELATION_FIELD_PRODUCT];
            $_data[$item[static::RELATION_FIELD_PRODUCT]] = $item;
        }

        return $_data;
    }

    /**
     * @param array $array
     * @param string $field
     * @return array
     */
    private function __getOnlyIds($array, $field)
    {
        $_array = [];
        if (!empty(is_array($array)) && is_array($array)) {
            $array = array_filter($array);
            foreach ($array as $item) {
                if (is_array($item) && array_key_exists($field, $item)) {
                    $_array[] = $item[$field];
                }
            }
        }

        return $_array;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     * @throws Exception
     */
    public function saveHotSpots(AbstractModel $object)
    {
        $id = (int)$object->getData(static::IDENTIFIER_FIELD);

        $connection = $this->getConnection();

        $oldData = $this->lookupHotSpots($id);
        $newData = $object->getData(IntsPostData::HOTSPOT);
        if (empty($newData)) {
            $newData = [];
        }
        $oldDataIds = $this->__getOnlyIds($oldData, static::RELATION_FIELD_HOTSPOT);
        $newDataIds = $this->__getOnlyIds($newData, static::RELATION_FIELD_HOTSPOT);

        $table = $this->getTable(static::RELATION_TABLE_HOTSPOT);

        $delete = array_diff($oldDataIds, $newDataIds);
        if ($delete) {
            $where = [
                static::IDENTIFIER_FIELD . ' = ?' => $id,
                static::RELATION_FIELD_HOTSPOT . ' IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newDataIds, $oldDataIds);
        $data = [];
        foreach ($newData as $item) {
            if (isset($item['id'])) {
                unset($item['id']);
            }
            if (in_array($item[static::RELATION_FIELD_HOTSPOT], $insert)) {
                $item[static::IDENTIFIER_FIELD] = $id;
                $data[] = $item;
            } else {
                $connection->update($table, $item, [
                    static::IDENTIFIER_FIELD . ' = ?' => $id,
                    static::RELATION_FIELD_HOTSPOT . ' = ?' => $item[static::RELATION_FIELD_HOTSPOT],
                ]);
            }
        }
        if ($data) {
            $connection->insertMultiple($table, $data);
        }

        return $object;
    }

    /**
     * @param int $id
     * @return array[]
     */
    public function lookupHotSpots($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable(static::RELATION_TABLE_HOTSPOT), '*')
            ->where(static::IDENTIFIER_FIELD . ' = :' . static::IDENTIFIER_FIELD)
            ->order('position');
        $data = $connection->fetchAll($select, [static::IDENTIFIER_FIELD => (int)$id]);
        $_data = [];
        foreach ($data as $item) {
            $item['id'] = $item[static::RELATION_FIELD_HOTSPOT];
            $_data[$item[static::RELATION_FIELD_HOTSPOT]] = $item;
        }

        return $_data;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('olegnax_instagramfeedpro_intspost', 'intspost_id');
    }

    /**
     * @param AbstractModel $object
     * @return IntsPost
     */
    protected function _beforeSave(AbstractModel $object)
    {

        $data = $object->getData(IntsPostData::CAPTION);
        if (empty($data)) {
            $object->setData(IntsPostData::CAPTION, '');
        }
        $data = $object->getData(IntsPostData::CAPTION . '_encoded');
        if (!empty($data)) {
            $data = (string)json_encode($data);
            $data = trim($data,'"');
            $object->setData(IntsPostData::CAPTION, $data);
        }

        $data = $object->getData(IntsPostData::CHILDREN);
        if (is_array($data)) {
            $data = $this->json->serialize($data);
            $object->setData(IntsPostData::CHILDREN, $data);
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return IntsPost
     * @throws Exception
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadRelation($object);

        $data = $object->getData(IntsPostData::CHILDREN);
        if (!empty($data) && !is_array($data)) {
            try {
                $data = $this->json->unserialize($data);
                $object->setData(IntsPostData::CHILDREN, $data);
            } catch (Exception $exception) {
                $object->setData(IntsPostData::CHILDREN, []);
            }
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @throws Exception
     */
    public function loadRelation(AbstractModel $object)
    {
        $this->readStores($object);
        $this->readProducts($object);
        $this->readHotSpots($object);
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     * @throws Exception
     */
    public function readStores(AbstractModel $object)
    {
        if ($id = (int)$object->getIntspostId()) {
            $data = $this->lookupStoreIds($id);
            $object->setData(IntsPostData::STORE_ID, $data);
        }

        return $object;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     */
    public function readProducts(AbstractModel $object)
    {
        if ($id = (int)$object->getIntspostId()) {
            $data = $this->lookupProducts($id);
            $object->setData(IntsPostData::RELATED, $data);
        }

        return $object;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractModel
     */
    public function readHotSpots(AbstractModel $object)
    {
        if ($id = (int)$object->getIntspostId()) {
            $data = $this->lookupHotSpots($id);
            $object->setData(IntsPostData::HOTSPOT, $data);
        }

        return $object;
    }
}
