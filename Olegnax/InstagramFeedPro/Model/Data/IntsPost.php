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
use Olegnax\InstagramFeedPro\Api\Data\IntsPostInterface;

class IntsPost extends AbstractExtensibleObject implements IntsPostInterface
{

    /**
     * Get intspost_id
     * @return string|null
     */
    public function getIntspostId()
    {
        return $this->_get(self::INTSPOST_ID);
    }

    /**
     * Set intspost_id
     * @param string $intspostId
     * @return IntsPostInterface
     */
    public function setIntspostId($intspostId)
    {
        return $this->setData(self::INTSPOST_ID, $intspostId);
    }

    /**
     * Get ints_id
     * @return string|null
     */
    public function getIntsId()
    {
        return $this->_get(self::INTS_ID);
    }

    /**
     * Set ints_id
     * @param string $intsId
     * @return IntsPostInterface
     */
    public function setIntsId($intsId)
    {
        return $this->setData(self::INTS_ID, $intsId);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return IntsPostInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get owner
     * @return string|null
     */
    public function getOwner()
    {
        return $this->_get(self::OWNER);
    }

    /**
     * Set owner
     * @param string $owner
     * @return IntsPostInterface
     */
    public function setOwner($owner)
    {
        return $this->setData(self::OWNER, $owner);
    }

    /**
     * Get media_type
     * @return string|null
     */
    public function getMediaType()
    {
        return $this->_get(self::MEDIA_TYPE);
    }

    /**
     * Set media_type
     * @param string $mediaType
     * @return IntsPostInterface
     */
    public function setMediaType($mediaType)
    {
        return $this->setData(self::MEDIA_TYPE, $mediaType);
    }

    /**
     * Get shortcode
     * @return string|null
     */
    public function getShortcode()
    {
        return $this->_get(self::SHORTCODE);
    }

    /**
     * Set shortcode
     * @param string $shortcode
     * @return IntsPostInterface
     */
    public function setShortcode($shortcode)
    {
        return $this->setData(self::SHORTCODE, $shortcode);
    }

    /**
     * Get code
     * @return string|null
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * Set code
     * @param string $code
     * @return IntsPostInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get caption
     * @return string|null
     */
    public function getCaption()
    {
        return $this->_get(self::CAPTION);
    }

    /**
     * Set caption
     * @param string $caption
     * @return IntsPostInterface
     */
    public function setCaption($caption)
    {
        return $this->setData(self::CAPTION, $caption);
    }

    /**
     * Get media_url
     * @return string|null
     */
    public function getMediaUrl()
    {
        return $this->_get(self::MEDIA_URL);
    }

    /**
     * Set media_url
     * @param string $mediaUrl
     * @return IntsPostInterface
     */
    public function setMediaUrl($mediaUrl)
    {
        return $this->setData(self::MEDIA_URL, $mediaUrl);
    }

    /**
     * Get thumbnail_url
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        return $this->_get(self::THUMBNAIL_URL);
    }

    /**
     * Set thumbnail_url
     * @param string $thumbnailUrl
     * @return IntsPostInterface
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        return $this->setData(self::THUMBNAIL_URL, $thumbnailUrl);
    }

    /**
     * Get children
     * @return string|null
     */
    public function getChildren()
    {
        return $this->_get(self::CHILDREN);
    }

    /**
     * Set children
     * @param string $children
     * @return IntsPostInterface
     */
    public function setChildren($children)
    {
        return $this->setData(self::CHILDREN, $children);
    }

    /**
     * Get comments_count
     * @return string|null
     */
    public function getCommentsCount()
    {
        return $this->_get(self::COMMENTS_COUNT);
    }

    /**
     * Set comments_count
     * @param string $commentsCount
     * @return IntsPostInterface
     */
    public function setCommentsCount($commentsCount)
    {
        return $this->setData(self::COMMENTS_COUNT, $commentsCount);
    }

    /**
     * Get like_count
     * @return string|null
     */
    public function getLikeCount()
    {
        return $this->_get(self::LIKE_COUNT);
    }

    /**
     * Set like_count
     * @param string $likeCount
     * @return IntsPostInterface
     */
    public function setLikeCount($likeCount)
    {
        return $this->setData(self::LIKE_COUNT, $likeCount);
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
     * @return IntsPostInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get timestamp
     * @return string|null
     */
    public function getTimestamp()
    {
        return $this->_get(self::TIMESTAMP);
    }

    /**
     * Set timestamp
     * @param string $timestamp
     * @return IntsPostInterface
     */
    public function setTimestamp($timestamp)
    {
        return $this->setData(self::TIMESTAMP, $timestamp);
    }

    /**
     * Get dimensions_width
     * @return string|null
     */
    public function getDimensionsWidth()
    {
        return $this->_get(self::DIMENSIONS_WIDTH);
    }

    /**
     * Set dimensions_width
     * @param string $dimensionsWidth
     * @return IntsPostInterface
     */
    public function setDimensionsWidth($dimensionsWidth)
    {
        return $this->setData(self::DIMENSIONS_WIDTH, $dimensionsWidth);
    }

    /**
     * Get dimensions_height
     * @return string|null
     */
    public function getDimensionsHeight()
    {
        return $this->_get(self::DIMENSIONS_HEIGHT);
    }

    /**
     * Set dimensions_height
     * @param string $dimensionsHeight
     * @return IntsPostInterface
     */
    public function setDimensionsHeight($dimensionsHeight)
    {
        return $this->setData(self::DIMENSIONS_HEIGHT, $dimensionsHeight);
    }

    /**
     * Get related
     * @return string|null
     */
    public function getRelated()
    {
        return $this->_get(self::RELATED);
    }

    /**
     * Set related
     * @param string $related
     * @return IntsPostInterface
     */
    public function setRelated($related)
    {
        return $this->setData(self::RELATED, $related);
    }
}
