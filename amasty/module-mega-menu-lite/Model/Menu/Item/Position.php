<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Item;

use Magento\Framework\Model\AbstractModel;

class Position extends AbstractModel
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position::class);
    }

    /**
     * @param $afterItemId
     * @return $this
     * @throws \Exception
     */
    public function move($afterItemId)
    {
        $this->getResource()->beginTransaction();
        try {
            $this->getResource()->changePosition($this, $afterItemId);
            $this->getResource()->commit();
        } catch (\Exception $e) {
            $this->getResource()->rollBack();
            throw $e;
        }

        return $this;
    }
}
