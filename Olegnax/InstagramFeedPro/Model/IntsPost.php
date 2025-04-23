<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsPostInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsPostInterfaceFactory;
use Olegnax\InstagramFeedPro\Model\Config\Source\MediaType;
use Olegnax\InstagramFeedPro\Model\Config\Source\Users;
use Olegnax\InstagramFeedPro\Model\Data\HotSpot as DataHotSpot;
use Olegnax\InstagramFeedPro\Model\Data\IntsUser as DataIntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\HotSpot\CollectionFactory as HotSpotCollectionFactory;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\Collection;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\CollectionFactory as IntsUserCollectionFactory;
use Zend_Db_Expr;

/**
 * @method string getCode()
 * @method int getCommentsCount()
 * @method int getDimensionsHeight()
 * @method int getDimensionsWidth()
 * @method string getIntsId()
 * @method string getIntspostId()
 * @method bool getIsActive()
 * @method int getLikeCount()
 * @method string getMediaType()
 * @method string getOwner()
 * @method string getShortcode()
 * @method string getTimestamp()
 * @method int[] getStoreId()
 * @method void setCaption($caption)
 * @method void setChildren($children)
 * @method void setCode($code)
 * @method void setCommentsCount($commentsCount)
 * @method void setDimensionsHeight($dimensionsHeight)
 * @method void setDimensionsWidth($dimensionsWidth)
 * @method void setIntsId($intsId)
 * @method void setIntspostId($intspostId)
 * @method void setIsActive($isActive)
 * @method void setLikeCount($likeCount)
 * @method void setMediaType($mediaType)
 * @method void setMediaUrl($mediaUrl)
 * @method void setOwner($owner)
 * @method void setRelated($related)
 * @method void setShortcode($shortcode)
 * @method void setStoreId($storeId)
 * @method void setThumbnailUrl($thumbnailUrl)
 * @method void setTimestamp($timestamp)
 */
class IntsPost extends AbstractModel
{
    const BASE_URL = "https://www.instagram.com/p/";

    protected $_eventPrefix = 'olegnax_instagramfeedpro_intspost';
    /**
     * @var IntsPostInterfaceFactory
     */
    protected $intspostDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var int
     */
    protected $_storeId;
    /**
     * @var File
     */
    protected $file;
    /**
     * @var IntsUser|null
     */
    protected $user;
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var HotSpotCollectionFactory
     */
    protected $hotSpotCollectionFactory;
    /**
     * @var IntsUserCollectionFactory
     */
    protected $intsUserCollectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param IntsPostInterfaceFactory $intspostDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\IntsPost $resource
     * @param Collection $resourceCollection
     * @param StoreManagerInterface $storeManager
     * @param IntsUserCollectionFactory $intsUserCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param HotSpotCollectionFactory $hotSpotCollectionFactory
     * @param File $file
     * @param DirectoryList $directoryList
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IntsPostInterfaceFactory $intspostDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\IntsPost $resource,
        Collection $resourceCollection,
        StoreManagerInterface $storeManager,
        IntsUserCollectionFactory $intsUserCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        HotSpotCollectionFactory $hotSpotCollectionFactory,
        File $file,
        DirectoryList $directoryList,
        array $data = [],
        Json $json = null
    ) {
        $this->storeManager = $storeManager;
        $this->intspostDataFactory = $intspostDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->intsUserCollectionFactory = $intsUserCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->hotSpotCollectionFactory = $hotSpotCollectionFactory;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve intspost model with intspost data
     * @return IntsPostInterface
     */
    public function getDataModel()
    {
        $intspostData = $this->getData();

        $intspostDataObject = $this->intspostDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $intspostDataObject,
            $intspostData,
            IntsPostInterface::class
        );

        return $intspostDataObject;
    }

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string $key
     * @param string|int $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if (in_array(
                $key,
                [
                    Data\IntsPost::RELATED,
                    Data\IntsPost::STORE_ID,
                    Data\IntsPost::HOTSPOT,
                ]
            )
            && !array_key_exists(
                $key,
                $this->_data
            )) {
            $this->_resource->loadRelation($this);
        }
        if (in_array(
                $key,
                [
                    Data\IntsPost::CAPTION . '_decoded',
                    '',
                ]
            )
            && !parent::getData(Data\IntsPost::CAPTION . '_decoded')
        ) {
            $data = parent::getData(Data\IntsPost::CAPTION, $index);
            $data = json_decode('"' . $data . '"');
            $this->setData(Data\IntsPost::CAPTION . '_decoded', $data);

        }

        return parent::getData($key, $index);
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        $caption = $this->getData(Data\IntsPost::CAPTION . '_decoded');
        return preg_replace("#\n#", "<br/>\n", (string)$caption);
    }
    
    /**
     * @return string
     */
    public function getCleanedCaption()
    {
        $caption = $this->getData(Data\IntsPost::CAPTION . '_decoded');
        
        // Remove non-printable characters (ASCII values 0-31 and 127)
        $caption = preg_replace('/[\x00-\x1F\x7F]/', ' ', (string)$caption);
        
        // Remove <br> and <br/> tags
        $caption = preg_replace('#<br\s*/?>#i', '', $caption);
        
        // Replace the @ symbol with 'at:'
        $caption = str_replace('@', 'at:', $caption);
    
        return $caption;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return MediaType::IMAGE == $this->getMediaType();
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        return MediaType::VIDEO == $this->getMediaType();
    }

    /**
     * @return bool
     */
    public function isAlbum()
    {
        return MediaType::CAROUSEL_ALBUM == $this->getMediaType();
    }

    /**
     * @param bool $multi
     * @return array|string
     * @throws LocalizedException
     */
    public function getImageUrl($multi = false)
    {
        $result = [];
        switch ($this->getMediaType()) {
            case MediaType::IMAGE:
                $result = [$this->getMediaUrl()];
                break;
            case MediaType::VIDEO:
                $result = [$this->getThumbnailUrl()];
                break;
            case MediaType::CAROUSEL_ALBUM:
                if ($multi) {
                    $result = $this->getChildrenUrls();
                    if (!is_array($result)) {
                        $result = [$result];
                    }
                    if (!in_array($this->getMediaUrl(), $result)) {
                        $result = array_merge([$this->getMediaUrl()], $result);
                    }
                } else {
                    $result = [$this->getMediaUrl()];
                }
                break;
        }
        return $multi ? $result : array_shift($result);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getMediaUrl()
    {
        return $this->prepareUrl($this->getMediaPath());
    }

    /**
     * @param $image
     * @return string
     * @throws LocalizedException
     */
    private function prepareUrl($image)
    {
        $url = "";
        if ($image) {
            $mediaBaseUrl = '';
            if (!preg_match('@^http@i', $image)) {
                $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                );
            }

            $url = $mediaBaseUrl . $image;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getMediaPath()
    {
        return $this->getData(Data\IntsPost::MEDIA_URL);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getThumbnailUrl()
    {
        return $this->prepareUrl($this->getThumbnailPath());
    }

    /**
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->getData(Data\IntsPost::THUMBNAIL_URL);
    }

    /**
     * @return array|string|null
     * @throws LocalizedException
     */
    public function getChildrenUrls()
    {
        $children = $this->getChildren();
        if (is_array($children) && !empty($children)) {
            foreach ($children as &$item) {
                $item = $this->prepareUrl($item);
            }
        } elseif (!empty($children)) {
            $children = $this->prepareUrl($children);
        }

        return $children;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        $data = $this->getData(Data\IntsPost::CHILDREN);
        if (!empty($data) && !is_array($data)) {
            try {
                $data = $this->json->unserialize($data);
                $this->setData(Data\IntsPost::CHILDREN, $data);
            } catch (Exception $exception) {
                $this->setData(Data\IntsPost::CHILDREN, []);
            }
        }
        return $this->getData(Data\IntsPost::CHILDREN);
    }

    /**
     * @return IntsUser|null
     */
    public function getUser()
    {
        if (!$this->user) {
            /** @var IntsUser $model */
            $model = ObjectManager::getInstance()->create(IntsUser::class);
            /** @var Users $users */
            $users = ObjectManager::getInstance()->create(Users::class);
            $usersId = $users->toIdArray();
            if (array_key_exists($this->getData(Data\IntsPost::OWNER), $usersId)) {
                $model->load($usersId[$this->getData(Data\IntsPost::OWNER)]);
                $this->user = $model;
            }
        }

        return $this->user;
    }

    /**
     * @return IntsUser|null
     */
    public function getIntsUser()
    {
        $collection = $this->intsUserCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter(DataIntsUser::USER_ID, $this->getData(Data\IntsPost::OWNER));
        foreach ($collection as $item) {
            return $item;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getFullId()
    {
        return $this->getIntsId() . '_' . $this->getData(Data\IntsPost::OWNER);
    }

    /**
     * @return HotSpot[]
     */
    public function getRelated()
    {
        return array_filter(
            array_merge(
                (array)$this->getRelatedProducts(),
                (array)$this->getRelatedHotSpot()
            )
        );
    }

    /**
     * @return HotSpot[]
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function getRelatedProducts()
    {
        if (!$this->hasData('related_products') || !is_array($this->getData('related_products'))) {
            $_data = [];
            try {
                $collection = $this->getCollectionRelatedProducts()->addAttributeToSelect('*');
                $data = $this->getData(Data\IntsPost::RELATED);
                if ($collection->count()) {
                    /** @var Product $item */
                    foreach ($collection as $item) {
                        if (array_key_exists($item->getId(), $data)) {
                            /** @var HotSpot $hotSpot */
                            $hotSpot = ObjectManager::getInstance()->create(HotSpot::class);
                            $hotSpotData = $data[$item->getId()];
                            $markerStyle = $hotSpotData[DataHotSpot::MARKERSTYLE];
                            $id = (int)$markerStyle;
                            if ($markerStyle == (string)$id) {
                                $hotSpot->load($id);
                            } else {
                                $hotSpot->setData(DataHotSpot::STATUS, '1');
                            }
                            $hotSpot->setData('product', $item);

                            foreach ($hotSpotData as $field => $value) {
                                if (
                                    (
                                        $markerStyle == (string)$id
                                        && DataHotSpot::MARKERSTYLE != $field
                                    )
                                    || $markerStyle != (string)$id
                                ) {
                                    $hotSpot->setData($field, $value);
                                }
                            }
                            $_data[] = $hotSpot;
                        }
                    }
                }
                $this->setData('related_products', $_data);
            } catch (NoSuchEntityException $e) {
                // @todo log error?
            }
        }

        return $this->getData('related_products');
    }

    /**
     * @param bool $isAdmin
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     *
     */
    public function getCollectionRelatedProducts($isAdmin = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();

        $products = $this->getData(Data\IntsPost::RELATED);
        $productIds = !empty($products) ? array_keys($products) : [];

        if (!empty($productIds)) {
            if ($this->getCurrentStoreId() && !$isAdmin) {
                $collection->addStoreFilter($this->getCurrentStoreId());
            }
            $collection->addIdFilter($productIds);
            $collection->getSelect()->order(new Zend_Db_Expr(sprintf(
                'FIELD(`' . $this->getTableAS($collection) . '`.`' . ResourceModel\IntsPost::RELATION_FIELD_PRODUCT . '`,%s)',
                implode(',', $productIds)
            )));
        } else {
            $collection->addFieldToFilter(
                ResourceModel\IntsPost::RELATION_FIELD_PRODUCT,
                0
            );
        }

        return $collection;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        if ($this->_storeId === null) {
            try {
                $this->_storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $this->_storeId = 0;
            }
        }
        return $this->_storeId;
    }

    /**
     * @param $collection
     * @return string
     */
    private function getTableAS($collection)
    {
        $data = $collection->getSelect()->getPart(Select::FROM);
        $keys = array_keys($data);
        return array_shift($keys);
    }

    /**
     * @return HotSpot[]
     */
    public function getRelatedHotSpot()
    {
        if (!$this->hasData('related_hot_spots') || !is_array($this->getData('related_hot_spots'))) {
            $collection = $this->getCollectionRelatedHotSpots()->addFieldToSelect('*');
            $data = $this->getData(Data\IntsPost::HOTSPOT);
            $_data = [];
            if ($collection->count()) {
                /** @var HotSpot $item */
                foreach ($collection as $item) {
                    if (array_key_exists($item->getId(), $data)) {
                        foreach ($data[$item->getId()] as $field => $value) {
                            $item->setData($field, $value);
                        }
                    }
                    $_data[] = $item;
                }
            }
            $this->setData('related_hot_spots', $_data);
        }
        return $this->getData('related_hot_spots');
    }

    /**
     * @param bool $isAdmin
     * @return ResourceModel\HotSpot\Collection
     */
    public function getCollectionRelatedHotSpots($isAdmin = false)
    {
        /** @var ResourceModel\HotSpot\Collection $collection */
        $collection = $this->hotSpotCollectionFactory->create();

        $hotSpot = $this->getData(Data\IntsPost::HOTSPOT);
        $hotSpotIds = !empty($hotSpot) ? array_keys($hotSpot) : [];

        if (!empty($hotSpotIds)) {
            $collection->addIdFilter($hotSpotIds);
            if (!$isAdmin) {
                $collection->addFieldToFilter(DataHotSpot::STATUS, '1');
            }
            $collection->getSelect()->order(new Zend_Db_Expr(sprintf(
                'FIELD(`' . $this->getTableAS($collection) . '`.`' . ResourceModel\IntsPost::RELATION_FIELD_HOTSPOT . '`,%s)',
                implode(',', $hotSpotIds)
            )));
        } else {
            $collection->addFieldToFilter(
                ResourceModel\IntsPost::RELATION_FIELD_HOTSPOT,
                0
            );
        }

        return $collection;
    }

    /**
     * @return bool
     */
    public function isExistRelated()
    {
        return (
                $this->hasData('related_products')
                && !empty($this->getData('related_products'))
            )
            || (
                $this->hasData('related_hot_spots')
                && !empty($this->getData('related_hot_spots'))
            )
            || 0 < $this->getCollectionRelatedProducts()->count()
            || 0 < $this->getCollectionRelatedHotSpots()->count();
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return static::BASE_URL . $this->getData(Data\IntsPost::SHORTCODE);
    }

    /**
     * @return IntsPost
     * @throws Exception
     */
    public function delete()
    {
        try {
            $this->removeMedia($this->getData(Data\IntsPost::MEDIA_URL));
            $this->removeMedia($this->getData(Data\IntsPost::THUMBNAIL_URL));
            $children = $this->getChildren();
            if (!empty($children) && is_array($children)) {
                foreach ($children as $item) {
                    $this->removeMedia($item);
                }
            }
            return parent::delete();
        } catch (Exception $exception) {
            $this->setIsActive(0);
            $this->save();
            throw  new Exception(sprintf(
                'The publication "%s" was not deleted because: %s',
                $this->getIntsId(),
                $exception->getMessage()
            ));
        }
    }

    /**
     * @param $media
     * @throws FileSystemException
     * @throws Exception
     */
    protected function removeMedia($media)
    {
        if (!empty($media)) {
            $newFileName = $this->getMediaDir() . '/' . $media;
            if (
                $this->file->fileExists($newFileName, true)
                && !$this->file->rm($newFileName)
            ) {
                throw new Exception(
                    'It is not possible to delete a media file: ' .
                    $media
                );
            }
        }
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA);
    }
}
