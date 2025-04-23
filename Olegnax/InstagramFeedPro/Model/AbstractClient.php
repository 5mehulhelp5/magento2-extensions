<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Psr\Log\LoggerInterface;

class AbstractClient
{
    /**
     * @var InstagramAPI
     */
    protected $instagramAPI;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Url
     */
    protected $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var string
     */
    protected $siteToken;
    /**
     * @var string
     */
    protected $oauthAuthorize;
    /**
     * @var string
     */
    protected $oauthRefresh;
    /**
     * @var string|null
     */
    protected $beforeCursors;
    /**
     * @var string|null
     */
    protected $afterCursors;
    /**
     * @var string|null
     */
    protected $nextUrl;
    /**
     * @var string|null
     */
    protected $previousUrl;
    /**
     * @var bool
     */
    private $mStatus;
    /**
     * @var bool
     */
    private $mDev;
    /**
     * @var bool
     */
    private $mSupportExpired;

    /**
     * AbstractClient constructor.
     * @param LoggerInterface $logger
     * @param Url $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     * @throws Exception
     */
    public function __construct(
        LoggerInterface $logger,
        Url $urlBuilder,
        StoreManagerInterface $storeManager,
        Helper $helper,
        $instagramAPI
    ) {
        $this->logger = $logger;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->instagramAPI = $instagramAPI;
        $this->oauthAuthorize =  $helper->getModuleConfig(static::OAUTH_AUTH_PATH) ?? '';
        $this->oauthRefresh = $helper->getModuleConfig(static::OAUTH_REFRESH_PATH) ?? '';
        $this->siteToken = $helper->generateToken();
        $b = $helper->get();
        $this->mStatus = !empty($b)
            && isset($b->data->the_key)
            && $b->data->the_key == $helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code')
            && $b->data->status == "active";
        $this->mDev = $this->mStatus && isset($b->notices->develop);
        $this->mSupportExpired = $this->mStatus && $b->data->has_expired;
    }

    /**
     * @param $store_id
     * @return string
     */
    public function getAuthUrl($store_id)
    {
        $url = $this->oauthAuthorize;
        $urlQuery = (bool)parse_url($url, PHP_URL_QUERY);
        $url .= ($urlQuery ? '&' : '?') . http_build_query($this->getAuthKeys($store_id));
        return $url;
    }

    /**
     * @param $store_id
     * @return array
     */
    public function getAuthKeys($store_id)
    {
        return [
            'referer' => $this->getRedirectUri(),
            'store_id' => $store_id,
            'token' => $this->siteToken,
        ];
    }

    /**
     * @return string|null
     */
    public function getRedirectUri()
    {
        return $this->urlBuilder->getUrl(static::FRONTEND_PATH_OAUTH);
    }

    /**
     * @param $accessToken
     * @return $this
     */
    public function setToken($accessToken)
    {
        $this->instagramAPI->setToken($accessToken);
        return $this;
    }

    /**
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->instagramAPI->setUserId($userId);
        return $this;
    }
    /**
     * @return array
     */
    public function getUserFields(): array {
        return ['id', 'username', 'profile_picture_url', 'account_type', 'media_count'];
    }

    /**
     * @param null|int $userId
     * @return bool|string
     * @throws Exception
     */
    public function getUsername( $userId = null )
    {
        return $this->instagramAPI->getUser($userId, $this->getUserFields());
    }

    /**
     * @return array
     */
    public function getUserMediaFields(): array {
        return [
            'id',
            'caption',
            'media_type',
            'media_url',
            'permalink',
            'thumbnail_url',
            'timestamp',
            'comments_count',
            'like_count',
            'shortcode',
            'children{media_url,thumbnail_url}',
        ];
    }

    /**
     * @param int|null $userId
     * @param string $after
     * @param string $before
     * @return array
     * @throws Exception
     */
    public function getUserMedia($userId = null, $after = "", $before = "")
    {
        $data = $this->instagramAPI->getUserMedia(
            $userId,
            $this->getUserMediaFields(),
            5 * (!$this->mDev ? 5 : 1),
            $after,
            $before
        );
        $this->beforeCursors = $this->afterCursors = $this->nextUrl = $this->previousUrl = null;
        if (isset($data['paging'])) {
            $this->beforeCursors = isset($data['paging']['cursors']['before'])
                ? $data['paging']['cursors']['before']
                : null;
            $this->afterCursors = isset($data['paging']['cursors']['after'])
                ? $data['paging']['cursors']['after']
                : null;
            $this->nextUrl = isset($data['paging']['next'])
                ? $data['paging']['next']
                : null;
            $this->previousUrl = isset($data['paging']['previous'])
                ? $data['paging']['previous']
                : null;
        }
        if (isset($data['data'])) {
            $data = $data['data'];
            if (empty($data)) {
                return [];
            }
            foreach ($data as &$post) {
                if (isset($post['children'])) {
                    $post['children'] = $this->prepareChildren($post['children']);
                }
                if (isset($post['permalink'])) {
                    $post['shortcode'] = basename(parse_url($post['permalink'], PHP_URL_PATH));
                }
                if (isset($post['timestamp'])) {
                    $post['timestamp'] = date_create_from_format(
                        'Y-m-d\TH:i:sP',
                        $post['timestamp']
                    )->format('Y-m-d H:i:s');
                }
                $post['owner'] = $this->instagramAPI->userId;
            }
            $data[0]['code'] = $this->beforeCursors;
            $data[count($data) - 1]['code'] = $this->afterCursors;

        } else {
            throw new Exception(__('Invalid content received')->getText());
        }

        return $data;
    }

    /**
     * @param array $children
     * @return array
     */
    private function prepareChildren($children)
    {
        $_children = [];
        if (isset($children['data'])) {
            $children = $children['data'];
            foreach ($children as $subPost) {

                $image = isset($subPost['thumbnail_url']) ? $subPost['thumbnail_url'] : $subPost['media_url'];
                if (empty($image)) {
                    try {
                        $newSubPost = $this->getMedia($subPost['id']);
                        $image = isset($newSubPost['thumbnail_url']) ? $newSubPost['thumbnail_url'] : $newSubPost['media_url'];
                    } catch (Exception $e) {
                        $this->logger->warning('Instagram sub post: ' . $e->getMessage());
                    }
                }
                $_children[$subPost['id']] = $image;
            }
        }

        return $_children;
    }

    /**
     * @param $mediaId
     * @param array $fields
     * @return bool|string
     * @throws Exception
     */
    public function getMedia($mediaId, $fields = [])
    {
        $post = $this->instagramAPI->getMedia(
            $mediaId,
            array_unique(array_merge(
                $fields,
                [
                    'media_url',
                    'thumbnail_url',
                ]
            ))
        );
        if (isset($post['thumbnail_url'])) {
            $post['media_url'] = $post['thumbnail_url'];
            unset($post['thumbnail_url']);
        }

        return $post;
    }

    /**
     * @return $this
     */
    public function resetRequestCount()
    {
        $this->instagramAPI->resetRequestCount();

        return $this;
    }

    /**
     * @return int
     */
    public function getRequestCount()
    {
        return $this->instagramAPI->getRequestCount();
    }

    /**
     * @return string|null
     */
    public function getNextUrl()
    {
        return $this->nextUrl;
    }

    /**
     * @return bool
     */
    public function issetNext()
    {
        return (bool)$this->nextUrl;
    }

    /**
     * @return string|null
     */
    public function getPreviousUrl()
    {
        return $this->previousUrl;
    }

    /**
     * @return bool
     */
    public function issetPrevious()
    {
        return (bool)$this->previousUrl;
    }

    /**
     * @return string|null
     */
    public function getBeforeCursors()
    {
        return $this->beforeCursors;
    }

    /**
     * @return string|null
     */
    public function getAfterCursors()
    {
        return $this->afterCursors;
    }

    /**
     * @return string|null
     */
    public function getTockenExpiration(){
        return $this->instagramAPI->getTockenExpiration();
    }
}
