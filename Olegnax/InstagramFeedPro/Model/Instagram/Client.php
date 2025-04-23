<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Instagram;

use Olegnax\InstagramFeedPro\Model\AbstractClient;
use Olegnax\InstagramFeedPro\Model\Instagram\GraphAPI;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Psr\Log\LoggerInterface;

class Client extends AbstractClient
{
    const FRONTEND_PATH_OAUTH = 'olegnax_instagram/api/oauth';
    const OAUTH_AUTH_PATH = 'oauth/authorize';
    const OAUTH_REFRESH_PATH = 'oauth/refresh';

    public function __construct(
        LoggerInterface $logger,
        Url $urlBuilder,
        StoreManagerInterface $storeManager,
        Helper $helper,
        GraphAPI $instagramAPI
    ) {
        parent::__construct($logger, $urlBuilder, $storeManager, $helper, $instagramAPI);
    }
}
