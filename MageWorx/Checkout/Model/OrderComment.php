<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderComment
 *
 * Entity for temporary storage of comments.
 */
class OrderComment extends AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\Checkout\Model\ResourceModel\OrderComment::class);
        $this->setIdFieldName('id');
    }
}
