<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Cron;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Serialize\Serializer\Json;
use Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost\Update;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Helper\Image;
use Olegnax\InstagramFeedPro\Model\Facebook\Client as FacebookClient;
use Olegnax\InstagramFeedPro\Model\Instagram\Client as InstagramClient;
use Olegnax\InstagramFeedPro\Model\Data\IntsUser as DataIntsUser;
use Olegnax\InstagramFeedPro\Model\IntsPost;
use Olegnax\InstagramFeedPro\Model\IntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser\CollectionFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Psr\Log\LoggerInterface;

class UpdatePosts
{

    const XML_PATH_ENABLE = 'cron/enabled';
    const XML_PATH_DISABLE = 'cron/enabled_only_new';
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var IntsPost
     */
    protected $model;
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var File
     */
    protected $file;
    /**
     * @var InstagramClient
     */
    protected $instagramClient;
    /**
     * @var FacebookClient
     */
    protected $facebookClient;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var int[]|array|null
     */
    private $requestsHistory;
    /**
     * @var EncryptorInterface $encryptor
     */
    protected $encryptor;
    /**
     * Constructor
     *
     * @param Helper $helper
     * @param Image $imageHelper
     * @param LoggerInterface $logger
     * @param IntsPost $model
     * @param DirectoryList $directoryList
     * @param File $file
     * @param InstagramClient $instagramclient
     * @param FacebookClient $facebookClient
     * @param CollectionFactory $collectionFactory
     * @param EncryptorInterface $encryptor
     * @param Json|null $json
     */
    public function __construct(
        Helper $helper,
        Image $imageHelper,
        LoggerInterface $logger,
        IntsPost $model,
        DirectoryList $directoryList,
        File $file,
        InstagramClient $instagramClient,
        FacebookClient $facebookClient,
        CollectionFactory $collectionFactory,
        EncryptorInterface $encryptor,
        Json $json = null
    ) {
        $this->helper = $helper;
        $this->imageHelper = $imageHelper;
        $this->logger = $logger;
        $this->model = $model;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->instagramClient = $instagramClient;
        $this->facebookClient = $facebookClient;
        $this->collectionFactory = $collectionFactory;
        $this->encryptor = $encryptor;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Execute the cron
     *
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()
            || !$this->helper->getModuleConfig(static::XML_PATH_ENABLE)
            || $this->helper->getModuleConfig(static::XML_PATH_DISABLE)
        ) {
            return;
        }

        if (0 < (int) $this->helper->getModuleConfig(Update::XML_PATH_BLOCK, 0) - time()) {
            return;
        }

        $collectionUsers = $this->collectionFactory->create()->addFieldToSelect('*')->addFieldToFilter('is_active', 1);
        $c = $this->helper->get();
        if (empty($c)
            || !isset($c->data->the_key)
            || $c->data->the_key !== $this->helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code')
            || $c->data->status !== "active") {
            $collectionUsers = null;
        }
        if ($collectionUsers && $collectionUsers->getSize()) {
            /** @var IntsUser[] $users */
            $users = $collectionUsers->getItems();
            /** @var IntsUser $user */
            foreach ($users as $user) {
                $username = $user->getUsername();
                try {
                    $result = $this->request($user);
                    if (!$result) {
                        throw new Exception(__('No posts were found!')->getText());
                    }
                    $data = $result;
                    while (isset($data['paging']['after'])) {
                        $data = $this->request($user, $data['paging']['after']);
                        $result['added'] += $data['added'];
                        $result['update'] += $data['update'];
                        $this->updateHistory($data['requests']);
                    }
                    $this->generateMessage($result, $username);
                } catch (Exception $e) {
                    $this->logger->error(sprintf(
                        'Cronjob Instagram User "%s": %s',
                        $username,
                        $e->getMessage()
                    ));
                }
            }
            $this->saveHistory();
        }
    }

    /**
     * @param IntsUser $user
     * @param string $after
     * @param string $before
     * @return array|null
     * @throws Exception
     * @noinspection PhpDeprecationInspection
     */
    public function request($user, $after = "", $before = "")
    {
        $username = $user->getUsername();
        $clientType = 'instagramClient';
        if (DataIntsUser::ACCOUNT_TYPE_BUSINESS == $user->getAccountType()) {
            $clientType = 'facebookClient';
        }
        $images = $this->$clientType
            ->resetRequestCount()
            ->setToken($this->encryptor->decrypt($user->getAccessToken()))
            ->setUserId($user->getUserId())
            ->getUserMedia(null, $after, $before);
        $added = 0;
        $update = 0;
        if (!empty($images)) {
            $existPosts = $this->getExistItems($user);
            foreach ($images as $item) {
                if (!isset($item['id'])) {
                    continue;
                }
                $id = $item['id'];
                try {
                    $exist = !empty($existPosts) && array_key_exists($id, $existPosts);
                    $_item = $this->prepareItem($item);
                    if ($exist) {
                        $dbItem = $this->model->load($existPosts[$id]);
                        $dbItem->addData($_item)->save();
                        $update++;
                    } else {
                        $this->model->setData($_item)->save();
                        $added++;
                    }
                } catch (Exception $e) {
                    $this->logger->error(sprintf(
                        'Instagram: User "%s" and Post "%s": %s',
                        $username,
                        $id,
                        $e->getMessage()
                    ), $item);
                }
            }
        }

        return [
            'added' => $added,
            'update' => $update,
            'requests' => $this->$clientType->getRequestCount(),
            'paging' => array_filter([
                'before' => $this->$clientType->issetPrevious() ? $this->$clientType->getBeforeCursors() : null,
                'after' => $this->$clientType->issetNext() ? $this->$clientType->getAfterCursors() : null,
            ]),
        ];
    }

    /**
     * @param IntsUser $user
     * @return array
     */
    private function getExistItems($user)
    {
        $data = [];
        $itemCollection = $user->getAllPosts();
        if ($itemCollection && $itemCollection->getSize()) {
            $items = $itemCollection->getItems();
            /** @var IntsPost $item */
            foreach ($items as $item) {
                $data[$item->getIntsId()] = $item->getID();
            }
        }
        return $data;
    }

    /**
     * @param array $item
     * @return array
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function prepareItem($item)
    {
        $item['ints_id'] = $item['id'];
        unset($item['id']);
        unset($item['permalink']);
        $item['media_url'] = $this->downloadMedia($item['media_url']);
        if (isset($item['thumbnail_url'])) {
            $item['thumbnail_url'] = $this->downloadMedia($item['thumbnail_url']);
        }
        if (isset($item['children'])) {
            foreach ($item['children'] as &$image) {
                $image = $this->downloadMedia($image);
            }
        }
        if (isset($item['caption'])) {
            $item['caption_encoded'] = $item['caption'];
        }
        if (empty($item['dimensions_width']) || empty($item['dimensions_height'])) {
            try {
                $_image = isset($item['thumbnail_url']) ? $item['thumbnail_url'] : $item['media_url'];
                if (!preg_match('@^http@i', $_image)) {
                    $this->imageHelper->init($_image);
                    $item['dimensions_width'] = $this->imageHelper->getOriginalWidth();
                    $item['dimensions_height'] = $this->imageHelper->getOriginalHeight();
                }
            } catch (Exception $e) {
                $this->logger->error("Instagram:" . $e);
            }
        }

        return $item;
    }

    /**
     * @param string $imageUrl
     * @param string $prefix
     * @return string
     * @throws FileSystemException
     */
    protected function downloadMedia($imageUrl, $prefix = '')
    {
        if (empty($imageUrl)) {
            return $imageUrl;
        }

        $fileName = $prefix . baseName(parse_url($imageUrl, PHP_URL_PATH));
        $tempFileName = $this->getMediaDirTmpDir() . $fileName;
        $newFileName = $this->getMediaDirDestDir() . $fileName;

        if ($this->file->fileExists($newFileName, true)) {
            return $this->prepareUploadFile($newFileName);
        }

        try {
            $this->file->checkAndCreateFolder(dirname($tempFileName));
            $file_data = $this->getSslImage($imageUrl);
            if (empty($file_data)) {
                throw new Exception(__( 'Unable to download image from "' . $imageUrl . '"!')->getText());
            }
            ob_start();
            $result = $this->file->write($tempFileName, $file_data, 0755);
            $echo = ob_get_clean();
            unset($file_data);
            if (!$result) {
                throw new Exception($echo);
            }
            $this->file->checkAndCreateFolder(dirname($newFileName));
            $result = $this->file->mv($tempFileName, $newFileName);
            if (!$result) {
                throw new Exception(__("it is not possible to move a file from '{$tempFileName}' to '{$newFileName}'")->getText());
            }
            return $this->prepareUploadFile($newFileName);
        } catch (Exception $e) {
            $this->logger->error(sprintf('Instagram: %s', $e->getMessage()));
        }

        return $imageUrl;
    }

    /**
     * @param string $url
     *
     * @return bool|string
     */
    function getSslImage($url) {
        $ch = curl_init();
        curl_setopt_array($ch,[
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL => $url,
            CURLOPT_REFERER => $url,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirTmpDir()
    {
        return $this->getMediaDir() . '/tmp/';
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA);
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirDestDir()
    {
        return $this->getMediaDir() . '/' . Update::UPLOAD_DIR . '/';
    }

    /**
     * @param string $path
     * @return string
     * @throws FileSystemException
     */
    protected function prepareUploadFile($path)
    {
        return str_replace($this->getMediaDir() . '/', '', $path);
    }

    /**
     * @param int $count
     * @return $this
     */
    private function updateHistory($count)
    {
        if (0 < $count) {
            if (empty($this->requestsHistory)) {
                $this->getHistory();
            }
            $this->requestsHistory[time()] = $count;
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function getHistory()
    {
        $this->requestsHistory = (array)$this->json->unserialize(
            $this->helper->getModuleConfig(Update::XML_PATH_REQUESTS,
                0)
                ?: '{}'
        );
        $this->requestsHistory = array_filter($this->requestsHistory);

        return $this;
    }

    /**
     * @param array $data
     * @param string $username
     */
    private function generateMessage($data, $username)
    {
        $messages = [];
        if (0 < $data['added']) {
            $messages[] = __("Added \"%1\" posts.", $data['added'], $username);
        }
        if (0 < $data['update']) {
            $messages[] = __("Updated \"%1\" posts.", $data['update'], $username);
        }
        if (!empty($messages)) {
            $this->logger->info(sprintf(
                'Cronjob Instagram: User "%s": %s',
                $username,
                implode(
                    " ",
                    $messages
                )
            ));
        }
    }

    /**
     * @return $this
     */
    private function saveHistory()
    {
        if (!empty($this->requestsHistory)) {
            $this->requestsHistory = array_filter(
                $this->requestsHistory
            );
            $block = $this->validateHistory();
            if ($block) {
                $blockTime = time() + $block;
                if ($blockTime > (int) $this->helper->getModuleConfig(Update::XML_PATH_BLOCK, 0)) {
                    $this->helper->setModuleConfig(
                        Update::XML_PATH_BLOCK,
                        $blockTime
                    );
                }
            }
            $this->helper->setModuleConfig(
                Update::XML_PATH_REQUESTS,
                $this->json->serialize(
                    $this->requestsHistory
                )
            );
        }
        $this->requestsHistory = [];

        return $this;
    }

    /**
     * @return int
     */
    private function validateHistory()
    {
        $requests = [];
        foreach (Update::BLOCK_RULES as $timeRule => $count) {
            $requests[$timeRule] = 0;
        }
        $timeMax = 7 * 24 * 60 * 60;
        $timeCurrent = time();
        $this->getHistory();
        foreach ($this->requestsHistory as $time => $i) {
            if ($time < $timeCurrent - $timeMax) {
                unset($this->requestsHistory[$time]);
                continue;
            }
            foreach (Update::BLOCK_RULES as $timeRule => $count) {
                if ($time >= $timeCurrent - $timeRule) {
                    $requests[$timeRule] += $i;
                }
            }
        }
        $block = [];
        foreach (Update::BLOCK_RULES as $timeRule => $count) {
            if ($requests[$timeRule] >= $count) {
                $block[$timeRule] = $timeRule * $requests[$timeRule] / $count;
            }
        }

        return min(0 < count($block) ? ((int)max($block)) * Update::K_BY_BLOCK_RULES : 0, 86400);
    }
}
