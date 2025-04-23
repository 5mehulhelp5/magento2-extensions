<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\ResourceModel\OrderComment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageWorx\Checkout\Model\OrderComment::class,
            \MageWorx\Checkout\Model\ResourceModel\OrderComment::class
        );
        $this->_setIdFieldName('id');
    }
}
