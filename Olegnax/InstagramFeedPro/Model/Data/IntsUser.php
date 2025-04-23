<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Olegnax\InstagramFeedPro\Api\Data\IntsUserInterface;

class IntsUser extends AbstractExtensibleObject implements IntsUserInterface
{

    /**
     * Get intsuser_id
     * @return string|null
     */
    public function getIntsuserId()
    {
        return $this->_get(self::INTSUSER_ID);
    }

    /**
     * Set intsuser_id
     * @param string $intsuserId
     * @return IntsUserInterface
     */
    public function setIntsuserId($intsuserId)
    {
        return $this->setData(self::INTSUSER_ID, $intsuserId);
    }

    /**
     * Get media_count
     * @return int
     */
    public function getMediaCount()
    {
        return (int)$this->_get(self::MEDIA_COUNT);
    }

    /**
     * Set media_count
     * @param int $mediaCount
     * @return IntsUserInterface
     */
    public function setMediaCount($mediaCount)
    {
        return $this->setData(self::MEDIA_COUNT, (int)$mediaCount);
    }

    /**
     * Get username
     * @return string|null
     */
    public function getUsername()
    {
        return $this->_get(self::USERNAME);
    }

    /**
     * Set username
     * @param string $username
     * @return IntsUserInterface
     */
    public function setUsername($username)
    {
        return $this->setData(self::USERNAME, $username);
    }

    /**
     * Get account_type
     * @return string|null
     */
    public function getAccountType()
    {
        return $this->_get(self::ACCOUNT_TYPE);
    }

    /**
     * Set account_type
     * @param string $accountType
     * @return IntsUserInterface
     */
    public function setAccountType($accountType)
    {
        return $this->setData(self::ACCOUNT_TYPE, $accountType);
    }

    /**
     * Get access_token
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->_get(self::ACCESS_TOKEN);
    }

    /**
     * Set access_token
     * @param string $accessToken
     * @return IntsUserInterface
     */
    public function setAccessToken($accessToken)
    {
        return $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    /**
     * Get expire
     * @return string|null
     */
    public function getExpire()
    {
        return $this->_get(self::EXPIRE);
    }

    /**
     * Set expire
     * @param string $expire
     * @return IntsUserInterface
     */
    public function setExpire($expire)
    {
        return $this->setData(self::EXPIRE, $expire);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return IntsUserInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->_get(self::CREATION_TIME);
    }

    /**
     * Set creation_time
     * @param string $creationTime
     * @return IntsUserInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * Set user_id
     * @param string $userId
     * @return IntsUserInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Get profile_picture
     * @return string|null
     */
    public function getProfilePicture()
    {
        return $this->_get(self::PROFILE_PICTURE);
    }

    /**
     * Set profile_picture
     * @param string $profilePicture
     * @return IntsUserInterface
     */
    public function setProfilePicture($profilePicture)
    {
        return $this->setData(self::PROFILE_PICTURE, $profilePicture);
    }
}
