<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Observer;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magefan\Blog\Model\PostRepository;
use Magefan\Blog\Model\PostFactory;
use Magefan\Blog\Model\CategoryFactory;
use Magefan\Blog\Model\TagFactory;
use Magefan\Blog\Model\Url as BlogUrl;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;

class UpdateUrlRewriteForEntity implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var PostFactory
     */
    private $postFactory;

    /**
     * @var UrlRewriteFactory
     */
    private $rewriteFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var TagFactory
     */
    private $tagFactory;

    /**
     * @var BlogUrl
     */
    private $blogUrl;

    /**
     * @var PostCollectionFactory
     */
    private $postCollectionFactory;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @param RequestInterface $request
     * @param PostRepository $postRepository
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param BlogUrl $blogUrl
     * @param UrlRewriteFactory $rewriteFactory
     * @param StoreManagerInterface $storeManager
     * @param PostCollectionFactory $postCollection
     * @param UrlFinderInterface $urlFinder
     */
    public function __construct(
        RequestInterface      $request,
        PostRepository        $postRepository,
        PostFactory           $postFactory,
        CategoryFactory           $categoryFactory,
        TagFactory $tagFactory,
        BlogUrl $blogUrl,
        UrlRewriteFactory     $rewriteFactory,
        StoreManagerInterface $storeManager,
        PostCollectionFactory $postCollectionFactory,
        UrlFinderInterface $urlFinder
    ) {
        $this->request = $request;
        $this->postRepository = $postRepository;
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
        $this->tagFactory = $tagFactory;
        $this->blogUrl = $blogUrl;
        $this->rewriteFactory = $rewriteFactory;
        $this->storeManager = $storeManager;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->urlFinder = $urlFinder;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException|Exception
     */
    public function execute(Observer $observer): void
    {
        if (!((int)$this->request->getParam('url_key_create_redirect'))) {
            return;
        }
        $object = $observer->getData('data_object');
        if (!$object || !$object->getId() || !$object->isActive()) {
            return;
        }

        if ($object->isObjectNew() || (
            !$object->dataHasChangedFor('identifier') /*&& !$object->dataHasChangedFor('path') */
        )) {
            return;
        }

        $storeIds = $object->getStores() ?: $object->getStoreIds();
        if (!$storeIds) {
            return;
        }
        if (in_array(0, $storeIds)) {
            $storeIds = [];
            foreach ($this->storeManager->getStores() as $store) {
                if ($store->getIsActive() && $store->getId()) {
                    $storeIds[] = $store->getId();
                }
            }
        }
        if (!$storeIds) {
            return;
        }

        $modelName = $this->request->getControllerName() . 'Factory';
        $oldObject = $this->$modelName->create()->load($object->getId());
        if (!$oldObject || !$oldObject->getId() || !$object->isActive()) {
            return;
        }

        foreach ($storeIds as $storeId) {
            if (!is_numeric($storeId)) {
                continue;
            }

            if ($oldObject instanceof \Magefan\Blog\Model\Category) {
                $this->updateCategoryPostUrls($object, $oldObject, $storeId);
            }

            $this->rewriteUrl($object, $oldObject, $storeId, $this->request->getControllerName());
        }
    }


    /**
     * @param $category
     * @param $oldCategory
     * @param $storeId
     * @return void
     * @throws Exception
     */
    private function updateCategoryPostUrls($category, $oldCategory, $storeId)
    {
        $postCollectionList = $this->postCollectionFactory->create()
            ->addActiveFilter()
            ->addCategoryFilter($oldCategory)
            ->addStoreFilter($storeId);

        foreach ($postCollectionList as $oldPostObject) {

            $postObject = $this->postFactory->create()->load($oldPostObject->getId());
            $postObject->setParentCategory($category);

            $this->rewriteUrl($postObject, $oldPostObject, $storeId, 'post');
        }
    }

    /**
     * @param $object
     * @param $oldObject
     * @param $storeId
     * @param $controllerName
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function rewriteUrl($object, $oldObject, $storeId, $controllerName)
    {
        $objectUrl = $this->getObjectUrl($object, $storeId, $controllerName);
        $oldObjectUrl = $this->getObjectUrl($oldObject, $storeId, $controllerName);

        if ($objectUrl == $oldObjectUrl) {
            return;
        }

        if (!$this->isNewUrlRewrite($oldObjectUrl, $storeId)) {
            return;
        }

        $urlRewriteModel = $this->rewriteFactory->create();
        $urlRewriteModel->setEntityType('custom')
            ->setStoreId($storeId)
            ->setIsSystem(0)
            ->setRedirectType(301)
            //->setEntityId($object->getId())
            ->setTargetPath($objectUrl)
            ->setRequestPath($oldObjectUrl);
        $urlRewriteModel->save();
    }

    /**
     * @param $object
     * @param $storeId
     * @param $controllerName
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getObjectUrl($object, $storeId, $controllerName)
    {
        $this->blogUrl->startStoreEmulation($this->storeManager->getStore($storeId));
        $url = $this->blogUrl->getUrlPath($object, $controllerName);
        $this->blogUrl->stopStoreEmulation();

        return $url;
    }

    /**
     * @param $requestPath
     * @param $storeId
     * @return bool
     */
    private function isNewUrlRewrite($requestPath, $storeId)
    {
        $filterData = [
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => $requestPath,
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID => $storeId,
        ];

        $rewrite = $this->urlFinder->findOneByData($filterData);

        if (!isset($rewrite)) {
            return true;
        }

        return false;
    }
}
