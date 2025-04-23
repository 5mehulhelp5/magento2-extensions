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
use Olegnax\InstagramFeedPro\Block\Adminhtml\Post\Helper\Form\Info\Content;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Info extends AbstractBlock
{
    /**
     * Gallery html id
     *
     * @var string
     */
    protected $htmlId = 'info_post';
    /**
     * @var string
     */
    protected $blockId = 'block_info';

    /**
     * @var string
     */
    protected $formName = 'olegnax_instagramfeedpro_intspost_form';
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Info constructor.
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
        $content = $this->getChildBlock('content');
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
