<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterface;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterfaceFactory;
use Olegnax\InstagramFeedPro\Model\Data\IntsPost as IntsPostData;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\Collection;

/**
 * @method int getExpire()
 * @method int getMediaCount()
 * @method string getAccessToken()
 * @method string getAccountType()
 * @method string getCreationTime()
 * @method string getIntsuserId()
 * @method string getIsActive()
 * @method string getProfilePicture()
 * @method string getUserId()
 * @method string getUsername()
 * @method void setAccessToken($accessToken)
 * @method void setAccountType($accountType)
 * @method void setCreationTime($creationTime)
 * @method void setExpire($expire)
 * @method void setIntsuserId($intsuserId)
 * @method void setIsActive($isActive)
 * @method void setMediaCount($mediaCount)
 * @method void setProfilePicture($profilePicture)
 * @method void setUserId($userId)
 * @method void setUsername($username)
 */
class IntsUser extends AbstractModel
{

    const BASE_URL = "https://www.instagram.com/";
    protected $_eventPrefix = 'olegnax_instagramfeedpro_intsuser';
    protected $dataObjectHelper;

    protected $intsuserDataFactory;
    /**
     * @var CollectionFactory
     */
    protected $postsFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param IntsUserInterfaceFactory $intsuserDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\IntsUser $resource
     * @param Collection $resourceCollection
     * @param CollectionFactory $postsFactory
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        IntsUserInterfaceFactory $intsuserDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\IntsUser $resource,
        Collection $resourceCollection,
        CollectionFactory $postsFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->intsuserDataFactory = $intsuserDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->postsFactory = $postsFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve intsuser model with intsuser data
     * @return IntsUserInterface
     */
    public function getDataModel()
    {
        $intsuserData = $this->getData();

        $intsuserDataObject = $this->intsuserDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $intsuserDataObject,
            $intsuserData,
            IntsUserInterface::class
        );

        return $intsuserDataObject;
    }

    /**
     * @param null $store_id
     * @return ResourceModel\IntsPost\Collection
     * @throws NoSuchEntityException
     */
    public function getPosts($store_id = null)
    {
        return $this->getAllActivePosts()
            ->addStoreFilter($store_id ? $this->storeManager->getStore($store_id) : null);
    }

    /**
     * @return ResourceModel\IntsPost\Collection
     */
    public function getAllActivePosts()
    {
        return $this->getAllPosts()->addFieldToFilter(IntsPostData::IS_ACTIVE, '1');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getProfilePictureUrl(){
        return $this->prepareUrl($this->getProfilePicture());
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
            $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );

            $url = $mediaBaseUrl . $image;
        }
        return $url;
    }

    /**
     * @return ResourceModel\IntsPost\Collection
     */
    public function getAllPosts()
    {
        return $this->postsFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter(IntsPostData::OWNER, $this->getUserId())
            ->setOrder(
                IntsPostData::TIMESTAMP,
                \Magento\Framework\Data\Collection::SORT_ORDER_DESC
            );
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return static::BASE_URL . $this->getData(Data\IntsUser::USERNAME) . '/';
    }

    /**
     * @return IntsUser
     * @throws Exception
     */
    public function delete()
    {
        try {
            /** @var ResourceModel\IntsPost\Collection $collection */
            $collection = $this->postsFactory->create();
            $collection->addFieldToFilter(IntsPostData::OWNER, $this->getUserId());
            foreach ($collection as $item) {
                $item->delete();
            }
            return parent::delete();
        } catch (Exception $exception) {
            $this->setIsActive(0);
            $this->save();
            throw  new Exception(sprintf(
                'The User "%s" is not deleted because: %s',
                $this->getUsername(),
                $exception->getMessage()
            ));
        }
    }
}
