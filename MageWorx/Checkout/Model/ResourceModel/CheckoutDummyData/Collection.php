<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData;

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
            \MageWorx\Checkout\Model\CheckoutDummyData::class,
            \MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData::class
        );
        $this->_setIdFieldName('id');
    }
}
