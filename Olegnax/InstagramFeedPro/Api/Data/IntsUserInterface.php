<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IntsUserInterface extends ExtensibleDataInterface
{

    const INTSUSER_ID = 'intsuser_id';
    const CREATION_TIME = 'creation_time';
    const IS_ACTIVE = 'is_active';
    const USER_ID = 'user_id';
    const USERNAME = 'username';
    const ACCOUNT_TYPE = 'account_type';
    const ACCESS_TOKEN = 'access_token';
    const EXPIRE = 'expire';
    const MEDIA_COUNT = 'media_count';
    const PROFILE_PICTURE = 'profile_picture';
    const ACCOUNT_TYPE_BUSINESS = 'FACEBOOK BUSINESS';

    /**
     * Get intsuser_id
     * @return string|null
     */
    public function getIntsuserId();

    /**
     * Set intsuser_id
     * @param string $intsuserId
     * @return IntsUserInterface
     */
    public function setIntsuserId($intsuserId);

    /**
     * Get username
     * @return string|null
     */
    public function getUsername();

    /**
     * Set username
     * @param string $username
     * @return IntsUserInterface
     */
    public function setUsername($username);

    /**
     * Get account_type
     * @return string|null
     */
    public function getAccountType();

    /**
     * Set account_type
     * @param string $accountType
     * @return IntsUserInterface
     */
    public function setAccountType($accountType);

    /**
     * Get access_token
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access_token
     * @param string $accessToken
     * @return IntsUserInterface
     */
    public function setAccessToken($accessToken);

    /**
     * Get media_count
     * @return int
     */
    public function getMediaCount();

    /**
     * Set media_count
     * @param int $mediaCount
     * @return IntsUserInterface
     */
    public function setMediaCount($mediaCount);

    /**
     * Get expire
     * @return string|null
     */
    public function getExpire();

    /**
     * Set expire
     * @param string $expire
     * @return IntsUserInterface
     */
    public function setExpire($expire);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return IntsUserInterface
     */
    public function setIsActive($isActive);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation_time
     * @param string $creationTime
     * @return IntsUserInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return IntsUserInterface
     */
    public function setUserId($userId);
    /**
     * Get profile_picture
     * @return string|null
     */
    public function getProfilePicture();

    /**
     * Set profile_picture
     * @param string $profilePicture
     * @return IntsUserInterface
     */
    public function setProfilePicture($profilePicture);
}
