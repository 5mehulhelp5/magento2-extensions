<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Stock\Customer;

use Magento\Framework\DB\Select;

class Collection extends \Magento\ProductAlert\Model\ResourceModel\Stock\Customer\Collection
{
    /**
     * @var bool
     */
    private $customerModeFlag = false;

    /**
     * New columns added. Order for this columns need be processed by magento.
     *
     * @var array
     */
    private $joinedColumns = [
        'website_id',
        'stock_name'
    ];

    /**
     * @param int $productId
     * @param int $websiteId
     *
     * @return $this|\Magento\ProductAlert\Model\ResourceModel\Stock\Customer\Collection
     */
    public function join($productId, $websiteId)
    {
        $this->getSelect()->joinRight(
            ['alert' => $this->getTable('product_alert_stock')],
            'alert.customer_id=e.entity_id',
            ['add_date', 'send_date', 'send_count', 'alert_stock_id', 'website_id',
                'status', 'guest_email' => 'email']
        )
            ->reset(Select::WHERE)
            ->where('alert.product_id=?', $productId)
            ->group('alert.email')->group('e.email');
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_stock_id');
        $this->addAttributeToSelect('*');
        $this->setIsCustomerMode(true);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setIsCustomerMode($value)
    {
        $this->customerModeFlag = (bool)$value;

        return $this;
    }

    /**
     * @param array|string $field
     * @param string $direction
     *
     * @return $this|\Magento\ProductAlert\Model\ResourceModel\Stock\Customer\Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->_orders[$field] = $direction;

        if ($field == "email") {
            $field = "guest_email";
        }

        if (!in_array($field, $this->joinedColumns)) {
            $this->getSelect()->order($field . ' ' . $direction);
        }

        return $this;
    }

    /**
     * @return Select
     */
    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Select::COLUMNS);
            $unionSelect->columns('e.entity_id');

            $unionSelect->reset(Select::ORDER);
            $unionSelect->reset(Select::LIMIT_COUNT);
            $unionSelect->reset(Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(['a' => $unionSelect], 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Get customer mode flag value
     *
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->customerModeFlag;
    }
}
