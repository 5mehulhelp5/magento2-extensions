<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\Post\Helper\Form\Gallery;

use Magento\Backend\Block\Widget;

class Content extends Widget
{
    /**
     * @var string
     */
    protected $_template = 'post/helper/gallery.phtml';

    /**
     * @return mixed
     * @noinspection PhpUndefinedMethodInspection
     */
    public function getImages()
    {
        return $this->getElement()->getImages();
    }
}
