<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Controller\Adminhtml\Category\Upload;

use Magefan\Blog\Controller\Adminhtml\Upload\Image\Action;

/**
 * Blog featured image upload controller
 */
class CategoryImg extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'category_img';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magefan_Blog::category_save');
    }
}
