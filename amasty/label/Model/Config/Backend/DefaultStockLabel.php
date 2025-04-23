<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Config\Backend;

use Amasty\Label\Model\Source\Status;

class DefaultStockLabel extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        if ($this->isValueChanged()) {
            $id = $this->getOldValue();

            if ($id) {
                $this->getData('changeStatus')->execute((int) $id, Status::INACTIVE);
            }
        }

        $id = $this->getValue();

        if ($id) {
            $this->getData('changeStatus')->execute((int) $id, Status::ACTIVE);
        }

        return parent::beforeSave();
    }
}
