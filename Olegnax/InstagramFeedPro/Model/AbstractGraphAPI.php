<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use stdClass;

class AbstractGraphAPI
{
    /**
     * @var int
     */
    protected $requestCount;
    /**
     * @var int
     */
    public $userId;
    /**
     * @var string
     */
    public $redirectUri;
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Instagram constructor.
     */
    public function __construct()
    {
        $this->resetRequestCount();
    }

    /**
     * @return $this
     */
    public function resetRequestCount()
    {
        $this->requestCount = 0;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }

    /**
     * @return $this
     */
    public function updateRequestCount()
    {
        $this->requestCount++;

        return $this;
    }

    /**
     * @param array $options
     * @param string $method
     * @param string $to
     *
     * @return bool|string|array|stdClass
     */
    protected function curl(
        $options,
        $method = "GET",
        $to = "string"
    ) {
        $default = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 0,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];
        $options = array_replace($default, $options);

        $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        switch ($options[CURLOPT_CUSTOMREQUEST]) {
            case "POST":
                break;
            case "GET":
            default:
                $options[CURLOPT_CUSTOMREQUEST] = "GET";
                if (isset($options[CURLOPT_POSTFIELDS])) {
                    if (is_array($options[CURLOPT_POSTFIELDS])) {
                        $options[CURLOPT_POSTFIELDS] = http_build_query($options[CURLOPT_POSTFIELDS]);
                    }
                    $options[CURLOPT_URL] .= "?" . (string)$options[CURLOPT_POSTFIELDS];
                }
        }
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        $this->updateRequestCount();

        switch ($to) {
            case "array":
                if (!empty($data)) {
                    $data = json_decode((string)$data, true);
                }
                break;
            case "object":
                if (!empty($data)) {
                    $data = json_decode((string)$data);
                }
        }

        return $data;
    }

    /**
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        if (0 < (int)$userId) {
            $this->userId = (int)$userId;
        }

        return $this;
    }

    /**
     * @param $accessToken
     * @return $this
     */
    public function setToken($accessToken)
    {
        if (!empty($accessToken)) {
            $this->accessToken = $accessToken;
        }

        return $this;
    }
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->accessToken;
    }

    /**
     * @param null $userId
     * @param array $fields
     * @return array
     * @throws Exception
     */
    public function getUser($userId = null, $fields = [])
    {
        if (empty($userId)) {
            $userId = $this->userId;
        }
        if (empty($userId)) {
            $userId = "me";
        }

        $data = $this->curl([
            CURLOPT_URL => static::URL_BASE_GRAPH . $userId,
            CURLOPT_POSTFIELDS => array_filter([
                "fields" => implode(",", $fields),
                "access_token" => $this->accessToken,
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

    /**
     * @param null $userId
     * @param array $fields
     * @param int $limit
     * @param string $after
     * @param string $before
     * @return array
     * @throws Exception
     */
    public function getUserMedia($userId = null, $fields = [], $limit = 25, $after = "", $before = "")
    {
        if (empty($userId)) {
            $userId = $this->userId;
        }
        $data = $this->curl([
            CURLOPT_URL => static::URL_BASE_GRAPH . $userId . '/media',
            CURLOPT_POSTFIELDS => array_filter([
                "fields" => implode(",", $fields),
                "access_token" => $this->accessToken,
                "pretty" => 1,
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

    /**
     * @param null $mediaId
     * @param array $fields
     * @return array
     * @throws Exception
     */
    public function getMedia($mediaId = null, $fields = [])
    {
        $data = $this->curl([
            CURLOPT_URL => static::URL_BASE_GRAPH . $mediaId,
            CURLOPT_POSTFIELDS => array_filter([
                "fields" => implode(",", $fields),
                "access_token" => $this->accessToken,
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
    /**
     *
     * @return string
     */
    public function getTockenExpiration(){
        return '';
    }
}
