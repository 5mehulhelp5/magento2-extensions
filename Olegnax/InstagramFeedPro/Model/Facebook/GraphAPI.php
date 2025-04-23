<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Facebook;

use Exception;
use stdClass;
use Olegnax\InstagramFeedPro\Model\AbstractGraphAPI;

class GraphAPI extends AbstractGraphAPI
{
    const URL_BASE_GRAPH = "https://graph.facebook.com/v21.0/";

    /**
     * https://graph.facebook.com/debug_token?input_token=$token&access_token=$token
     * @return string
     */
    public function getTockenExpiration(){
        $result = '';
        $data = $this->curl([
            CURLOPT_URL => static::URL_BASE_GRAPH . 'debug_token',
            CURLOPT_POSTFIELDS => array_filter([
                "input_token" => $this->accessToken,
                "access_token" => $this->accessToken,
            ]),
        ], "get", "array");

        if ($data !== null && isset($data['data']['expires_at'])) {
            $result = $data['data']['expires_at'];
        }
        return $result;
    }
   
    /**
     * @param int|null $userId
     * @param array $fields
     * @param int $limit
     * @param string $after
     * @param string $before
     * @return array|bool|stdClass|string
     * @throws Exception
     */
    public function getAccounts($userId = null, $fields = [], $limit = 25, $after = "", $before = "")
    {
        if (empty($userId)) {
            $userId = "me";
        }
        $data = $this->curl([
            CURLOPT_URL => static::URL_BASE_GRAPH . $userId . '/accounts',
            CURLOPT_POSTFIELDS => array_filter([
                "fields" => implode(",", $fields),
                "access_token" => $this->accessToken,
                "limit" => $limit,
                "after" => $after,
                "before" => $before,
            ]),
        ], "get", "array");

        if (isset($data["error"])) {
            throw new Exception(
                $data["error"]["message"],
                isset($data["error"]["code"])
                    ? $data["error"]["code"]
                    : 500
            );
        }

        return $data;
    }
}
