<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IntsPostInterface extends ExtensibleDataInterface
{

    const STORE_ID = 'store_id';
    const CAPTION = 'caption';
    const THUMBNAIL_URL = 'thumbnail_url';
    const MEDIA_TYPE = 'media_type';
    const TIMESTAMP = 'timestamp';
    const SHORTCODE = 'shortcode';
    const INTS_ID = 'ints_id';
    const CODE = 'code';
    const OWNER = 'owner';
    const MEDIA_URL = 'media_url';
    const INTSPOST_ID = 'intspost_id';
    const COMMENTS_COUNT = 'comments_count';
    const CHILDREN = 'children';
    const LIKE_COUNT = 'like_count';
    const IS_ACTIVE = 'is_active';
    const RELATED = 'related';
    const HOTSPOT = 'hotspot';
    const DIMENSIONS_WIDTH = 'dimensions_width';
    const DIMENSIONS_HEIGHT = 'dimensions_height';

    /**
     * Get intspost_id
     * @return string|null
     */
    public function getIntspostId();

    /**
     * Set intspost_id
     * @param string $intspostId
     * @return IntsPostInterface
     */
    public function setIntspostId($intspostId);

    /**
     * Get dimensions_height
     * @return string|null
     */
    public function getDimensionsHeight();

    /**
     * Set dimensions_height
     * @param string $dimensionsHeight
     * @return IntsPostInterface
     */
    public function setDimensionsHeight($dimensionsHeight);

    /**
     * Get dimensions_width
     * @return string|null
     */
    public function getDimensionsWidth();

    /**
     * Set dimensions_width
     * @param string $dimensionsWidth
     * @return IntsPostInterface
     */
    public function setDimensionsWidth($dimensionsWidth);

    /**
     * Get ints_id
     * @return string|null
     */
    public function getIntsId();

    /**
     * Set ints_id
     * @param string $intsId
     * @return IntsPostInterface
     */
    public function setIntsId($intsId);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return IntsPostInterface
     */
    public function setStoreId($storeId);

    /**
     * Get owner
     * @return string|null
     */
    public function getOwner();

    /**
     * Set owner
     * @param string $owner
     * @return IntsPostInterface
     */
    public function setOwner($owner);

    /**
     * Get media_type
     * @return string|null
     */
    public function getMediaType();

    /**
     * Set media_type
     * @param string $mediaType
     * @return IntsPostInterface
     */
    public function setMediaType($mediaType);

    /**
     * Get shortcode
     * @return string|null
     */
    public function getShortcode();

    /**
     * Set shortcode
     * @param string $shortcode
     * @return IntsPostInterface
     */
    public function setShortcode($shortcode);

    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return IntsPostInterface
     */
    public function setCode($code);

    /**
     * Get caption
     * @return string|null
     */
    public function getCaption();

    /**
     * Set caption
     * @param string $caption
     * @return IntsPostInterface
     */
    public function setCaption($caption);

    /**
     * Get media_url
     * @return string|null
     */
    public function getMediaUrl();

    /**
     * Set media_url
     * @param string $mediaUrl
     * @return IntsPostInterface
     */
    public function setMediaUrl($mediaUrl);

    /**
     * Get thumbnail_url
     * @return string|null
     */
    public function getThumbnailUrl();

    /**
     * Set thumbnail_url
     * @param string $thumbnailUrl
     * @return IntsPostInterface
     */
    public function setThumbnailUrl($thumbnailUrl);

    /**
     * Get children
     * @return string|null
     */
    public function getChildren();

    /**
     * Set children
     * @param string $children
     * @return IntsPostInterface
     */
    public function setChildren($children);

    /**
     * Get comments_count
     * @return string|null
     */
    public function getCommentsCount();

    /**
     * Set comments_count
     * @param string $commentsCount
     * @return IntsPostInterface
     */
    public function setCommentsCount($commentsCount);

    /**
     * Get like_count
     * @return string|null
     */
    public function getLikeCount();

    /**
     * Set like_count
     * @param string $likeCount
     * @return IntsPostInterface
     */
    public function setLikeCount($likeCount);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return IntsPostInterface
     */
    public function setIsActive($isActive);

    /**
     * Get timestamp
     * @return string|null
     */
    public function getTimestamp();

    /**
     * Set timestamp
     * @param string $timestamp
     * @return IntsPostInterface
     */
    public function setTimestamp($timestamp);

    /**
     * Get related
     * @return string|null
     */
    public function getRelated();

    /**
     * Set related
     * @param string $related
     * @return IntsPostInterface
     */
    public function setRelated($related);
}
