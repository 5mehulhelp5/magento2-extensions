<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Config\Backend;

use Magento\Framework\App\Config\Value;

class StockStatus extends Value
{
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $id = $this->getData('config')->getDefaultStockLabelId();

            if (null !== $id) {
                $status = $this->getValue();
                $this->getData('changeStatus')->execute($id, (int) $status);
            }
        }

        return parent::beforeSave();
    }
}
