<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\Post\Helper\Form\Info;

use Magento\Backend\Block\Widget;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Content extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'post/helper/info.phtml';

    /**
     * Get post
     *
     * @return IntsPost
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getIntsPost()
    {
        return $this->getElement()->getIntsPost();
    }
}
