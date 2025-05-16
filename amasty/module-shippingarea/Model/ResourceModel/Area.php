<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\ResourceModel;

use Amasty\ShippingArea\Api\Data\AreaInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class Area extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public const MAIN_TABLE = 'amasty_shipping_area';
    public const DELIMITER = ',';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, AreaInterface::AREA_ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\ShippingArea\Model\Area $object
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data = $this->prepareOptionalFields($object->getData());

        //use object manager to avoid loading dependencies of parent class
        $objectManager = ObjectManager::getInstance();
        $serializer = $objectManager->create(Json::class);

        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                if ($key == AreaInterface::POSTCODE_SET) {
                    $value = $serializer->serialize($value);
                } else {
                    $value = implode(self::DELIMITER, $value);
                }
            }
        }
        $object->setData($data);

        return parent::_beforeSave($object);
    }

    /**
     * @param array|null $data
     *
     * @return array
     */
    private function prepareOptionalFields($data)
    {
        if (!$data) {
            return [];
        }

        $data[AreaInterface::COUNTRY_SET] = $data[AreaInterface::COUNTRY_CONDITION]
            ? $data[AreaInterface::COUNTRY_SET] : null;
        $data[AreaInterface::CITY_SET] = $data[AreaInterface::CITY_CONDITION]
            ? $data[AreaInterface::CITY_SET] : null;
        $data[AreaInterface::POSTCODE_SET] = $data[AreaInterface::POSTCODE_CONDITION]
            ? $data[AreaInterface::POSTCODE_SET] : null;
        $data[AreaInterface::ADDRESS_SET] = $data[AreaInterface::ADDRESS_CONDITION]
            ? $data[AreaInterface::ADDRESS_SET] : null;

        if (!$data[AreaInterface::STATE_CONDITION]) {
            $data[AreaInterface::STATE_SET_LISTING] = $data[AreaInterface::STATE_SET] = null;
        } elseif ($data[AreaInterface::STATE_SET_LISTING]) {
            $data[AreaInterface::STATE_SET] = null;
        } else {
            $data[AreaInterface::STATE_SET_LISTING] = null;
        }

        return $data;
    }
}
