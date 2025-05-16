<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Model\ResourceModel\OptionSetting;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Helper\FilterSetting;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting as OptionSettingResource;
use Magento\Framework\DB\Select;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Store\Model\Store;

/**
 * OptionSetting Collection
 * @method OptionSettingResource getResource()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var Option\CollectionFactory
     */
    private $optionCollectionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Option\CollectionFactory $optionCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->optionCollectionFactory = $optionCollectionFactory;
    }

    /**
     * Collection protected constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\ShopbyBase\Model\OptionSetting::class,
            OptionSettingResource::class
        );
        $this->_idFieldName = $this->getResource()->getIdFieldName();
    }

    /**
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @return $this
     * @deprecated use addLoadFilters with attribute code
     */
    public function addLoadParams($filterCode, $optionId, $storeId)
    {
        return $this->addLoadFilters(
            FilterSetting::convertToAttributeCode($filterCode),
            (int) $optionId,
            (int) $storeId
        );
    }

    /**
     * @return $this
     */
    public function addLoadFilters(string $attributeCode, int $optionId, int $storeId = Store::DEFAULT_STORE_ID)
    {
        $listStores = [Store::DEFAULT_STORE_ID];
        if ($storeId > Store::DEFAULT_STORE_ID) {
            $listStores[] = $storeId;
        }

        $this->addFieldToFilter(OptionSettingInterface::ATTRIBUTE_CODE, $attributeCode)
            ->addFieldToFilter('value', $optionId)
            ->addFieldToFilter('store_id', $listStores)
            ->addOrder('store_id', self::SORT_ORDER_DESC);

        return $this;
    }

    /**
     * @param int $storeId
     * @return array
     * @deprecared moved to Resource model
     */
    public function getHardcodedAliases($storeId)
    {
        return $this->getResource()->getHardcodedAliases($storeId);
    }

    /**
     * @param $value
     * @param $storeId
     * @return mixed
     */
    public function getValueFromMagentoEav($value, $storeId)
    {
        $optionCollection = $this->optionCollectionFactory->create()
            ->addFieldToFilter('main_table.option_id', $value)
            ->addFieldToFilter('option.store_id', [0, $storeId])
            ->setOrder('option.store_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);
        $optionCollection->getSelect()
            ->setPart('columns', [])
            ->join(
                ['option' => $optionCollection->getTable('eav_attribute_option_value')],
                'option.option_id = main_table.option_id',
                ['value']
            );

        return $this->getConnection()->fetchOne($optionCollection->getSelect());
    }

    /**
     * @return $this
     */
    public function addTitleToCollection()
    {
        $this->getSelect()->joinInner(
            ['amshopbybrand_option' => $this->getTable('eav_attribute_option')],
            'main_table.value = amshopbybrand_option.option_id',
            []
        );
        $this->join(
            ['option' => $this->getTable('eav_attribute_option_value')],
            'option.option_id = main_table.value'
        );
        $this->getSelect()->columns('IF(main_table.title = "", option.value, main_table.title) as title');
        $this->getSelect()->columns(
            'IF(main_table.meta_title = "", option.value, main_table.meta_title) as meta_title'
        );
        $this->getSelect()->group('option_setting_id');

        return $this;
    }
}
