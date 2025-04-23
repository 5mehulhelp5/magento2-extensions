<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Item;

use IntlDateFormatter;
use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Olegnax\InstagramFeedPro\Helper\Image;
use Olegnax\InstagramFeedPro\Model\Config\Source\Users;
use Olegnax\InstagramFeedPro\Model\IntsPost;

/**
 * @method IntsPost getPost()
 * @method int getDateFormat()
 */
class MediaType extends Template
{
    /**
     * @var string
     */
    const TEMPLATE = 'item/%s.phtml';
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var array
     */
    protected $users;
    /**
     * @var Json|mixed|null
     */
    protected $json;

    /**
     * MediaType constructor.
     * @param Context $context
     * @param Image $imageHelper
     * @param Users $users
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        Users $users,
        array $data = [],
        Json $json = null
    ) {
        $this->imageHelper = $imageHelper;
        $this->users = $users->toArray();
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $data);
    }

    /**
     * @param $file
     * @param $size
     * @return Image
     * @throws Exception
     */
    public function getResizedImage($file, $size)
    {
        return $this->imageHelper->init($file)->adaptiveResize($size);
    }

    /**
     *
     */
    protected function _construct()
    {
        $widget = $this->getData('widget');
        $type = strtolower($this->getPost()->getMediaType());
        if (isset($widget['images_only']) && $widget['images_only']) {
            $type = 'image';
        }
        $this->addData([
            'template' => sprintf('item/%s.phtml', $type),
        ]);
        parent::_construct();
    }

    /**
     * @param IntsPost $post
     * @return string
     * @throws LocalizedException
     */
    public function getJSON(IntsPost $post)
    {
        $data = $post->getData();
        foreach (['code', 'store_id', 'is_active', 'ints_id'] as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }
        foreach (['intspost_id', 'dimensions_width', 'dimensions_height', 'comments_count', 'like_count'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = abs((int)$data[$key]);
            }
        }
        if (array_key_exists($data['owner'], $this->users)) {
            $data['owner_name'] = $this->users[$data['owner']];
        }
        $data['related'] = $post->isExistRelated();
        $data['full_id'] = $post->getFullId();
        $data['media_url'] = $post->getMediaUrl();
        $data['url'] = $post->getURL();
        $data['thumbnail_url'] = $post->getThumbnailUrl();
        $data['children'] = $post->getChildrenUrls();
        $format = $this->getDateFormat() ?: IntlDateFormatter::SHORT;
        $data['timestamp'] = $this->formatDate($post->getTimestamp(), $format);
        $owner = $post->getIntsUser();
        $data['owner_profile_picture'] = $owner ? $owner->getProfilePicture() : null;

        return str_replace("'", "", (string)$this->json->serialize($data));
    }

    /**
     * @return bool
     */
    public function isLazyLoadEnabled()
    {
        return (bool) $this->getData('lazy_load_enabled');
    }

}
