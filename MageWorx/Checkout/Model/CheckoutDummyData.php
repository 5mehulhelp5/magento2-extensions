<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model;

use Magento\Framework\Model\AbstractModel;

class CheckoutDummyData extends AbstractModel
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\Checkout\Model\ResourceModel\CheckoutDummyData::class);
        $this->setIdFieldName('id');
    }
}
