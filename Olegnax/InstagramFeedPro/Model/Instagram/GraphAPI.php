<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Instagram;

use Olegnax\InstagramFeedPro\Model\AbstractGraphAPI;

class GraphAPI extends AbstractGraphAPI
{
    const URL_BASE_API = "https://api.instagram.com/";
    const URL_BASE_GRAPH = "https://graph.instagram.com/";

    const URL_PATH_AUTHORIZE = "oauth/authorize";
    const URL_PATH_ACCESSTOKEN = "oauth/access_token";
}
