<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Facebook;

use Olegnax\InstagramFeedPro\Model\AbstractClient;
use Olegnax\InstagramFeedPro\Model\Facebook\GraphAPI;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Psr\Log\LoggerInterface;

class Client extends AbstractClient
{
    const FRONTEND_PATH_OAUTH = 'olegnax_instagram/api/oauthgraph';
    const OAUTH_AUTH_PATH = 'oauth_graph/authorize';
    const OAUTH_REFRESH_PATH = 'oauth_graph/refresh';

    public function __construct(
        LoggerInterface $logger,
        Url $urlBuilder,
        StoreManagerInterface $storeManager,
        Helper $helper,
        GraphAPI $instagramAPI
    ) {
        parent::__construct($logger, $urlBuilder, $storeManager, $helper, $instagramAPI);
    }
    /**
     * @return array
     * @throws Exception
     */
    public function getInstagramBusinessAccounts()
    {
        $data = [];
        $after = '';
        do {
            $response = $this->instagramAPI->getAccounts(
                null,
                ['id', 'access_token', 'instagram_business_account'],
                25,
                $after
            );
            if (isset($response['paging']) && isset($response['paging']['next'])) {
                $after = $response['paging']['cursors']['after'];
            }
            if (isset($response['data'])) {
                $data = array_merge($data, $response['data']);
            }

        } while (!empty($after));

        $result = [];
        foreach ($data as $page) {
            if (array_key_exists('instagram_business_account', $page)) {
                $_data = $page['instagram_business_account'];
                $_data['access_token'] = array_key_exists('access_token', $page)
                    ? $page['access_token'] :
                    $this->instagramAPI->getToken();
                $result[] = $_data;
            }
        }
        return $result;
    }
    
    public function getUserFields(): array {
        return ['id', 'username', 'profile_picture_url', 'media_count'];
    }
}
