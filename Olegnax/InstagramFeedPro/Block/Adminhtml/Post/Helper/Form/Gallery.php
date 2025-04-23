<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\Post\Helper\Form;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Olegnax\InstagramFeedPro\Block\Adminhtml\Post\Helper\Form\Gallery\Content;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Gallery extends AbstractBlock
{
    /**
     * Gallery html id
     *
     * @var string
     */
    protected $htmlId = 'media_gallery';
    /**
     * @var string
     */
    protected $blockId = 'block_gallery';

    /**
     * @var string
     */
    protected $formName = 'olegnax_instagramfeedpro_intspost_form';
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Gallery constructor.
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get product images
     *
     * @return array
     * @throws LocalizedException
     */
    public function getImages()
    {
        $result = [];
        /** @var IntsPost $intsPost */
        $intsPost = $this->getIntsPost();
        if ($intsPost->isVideo()) {
            $image = $intsPost->getThumbnailUrl();
            $video = $intsPost->getMediaUrl();
            $result[] = [
                'label' => basename($image),
                'url' => $image,
                'video' => $video,
            ];
        } else {
            $gallery = $intsPost->getImageUrl(true);
            if (!empty($gallery) && is_array($gallery)) {
                foreach ($gallery as $image) {
                    $result[] = [
                        'label' => basename($image),
                        'url' => $image,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get post
     *
     * @return IntsPost
     */
    public function getIntsPost()
    {
        return $this->registry->registry('olegnax_instagramfeedpro_intspost');
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function toHtml()
    {
        $content = $this->getChildBlock('media_content');
        if (!$content) {
            $content = $this->getLayout()->createBlock(
                Content::class,
                '',
                [
                    'config' => [
                        'parentComponent' => implode('.', [
                            $this->formName,
                            $this->formName,
                            $this->blockId,
                            $this->blockId,
                        ]),
                    ],
                ]
            );
        }

        $content
            ->setId($this->getHtmlId() . '_content')
            ->setElement($this)
            ->setFormName($this->formName);
        return $content->toHtml();
    }

    /**
     * @return string
     */
    protected function getHtmlId()
    {
        return $this->htmlId;
    }
}
