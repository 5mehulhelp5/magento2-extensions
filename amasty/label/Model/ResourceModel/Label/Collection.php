<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label;
use Amasty\Label\Model\ResourceModel\Label as LabelResource;
use Amasty\Label\Model\Source\Status;
use Amasty\Label\Setup\Uninstall;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * @method LabelInterface[] getItems()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends AbstractCollection
{
    public const MODE_LIST = 1;
    public const MODE_PDP = 2;
    public const STORE_ID_FIELD = 'store_id';
    public const CUSTOMER_GROUP_ID = 'customer_group_id';

    /**
     * @var int|null
     */
    private $currentMode = null;

    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_label_entity_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * @var int[]
     */
    private $stores = [];

    /**
     * @var int[]
     */
    private $customerGroups = [];

    /**
     * @var Json
     */
    private $serializer;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        Json $serializer = null // TODO move to not optional argument and remove OM
    ) {
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->serializer = $serializer ?? \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    protected function _construct(): void
    {
        $this->_init(Label::class, LabelResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
        $this->addFilterToMap(LabelInterface::LABEL_ID, 'main_table.' . LabelInterface::LABEL_ID);
    }

    public function addActiveFilter(): void
    {
        $this->addFieldToFilter(LabelInterface::STATUS, Status::ACTIVE);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        $field = is_string($field) && strpos($field, '.') === false ? "main_table.{$field}" : $field;
        parent::addFieldToFilter($field, $condition); // prevent ambiguous sql errors

        return $this;
    }

    /**
     * @param int|array $storeFilter
     */
    public function addStoreFilter($storeFilter): void
    {
        $this->stores = array_merge($this->stores, (array)$storeFilter);
    }

    /**
     * @param int|array $customerFilter
     */
    public function addCustomerGroupFilter($customerFilter): void
    {
        $this->customerGroups = array_merge($this->customerGroups, (array)$customerFilter);
    }

    protected function _renderFiltersBefore(): void
    {
        $this->extensionAttributesJoinProcessor->process($this);
        parent::_renderFiltersBefore();
    }

    protected function _renderFilters()
    {
        if (!$this->_isFiltersRendered) {
            $this->filterByRelatedEntity(
                Uninstall::AMASTY_LABEL_STORE_TABLE,
                $this->stores,
                self::STORE_ID_FIELD
            );
            $this->filterByRelatedEntity(
                Uninstall::AMASTY_LABEL_CUSTOMER_GROUP_TABLE,
                $this->customerGroups,
                self::CUSTOMER_GROUP_ID
            );
        }

        return parent::_renderFilters();
    }

    /**
     * Joins related table and adds filter by ids of related entity
     *
     * @param string $tableName
     * @param int[] $values
     * @param string $conditionColumn
     */
    private function filterByRelatedEntity(string $tableName, array $values, string $conditionColumn): void
    {
        if (count($values) > 0) {
            $select = $this->getSelect();
            $tableName = $this->getTable($tableName);
            $select->join(
                $tableName,
                sprintf('main_table.%1$s = %2$s.%1$s', LabelInterface::LABEL_ID, $tableName),
                []
            );
            $values = array_map('intval', $values);
            $isSingleValue = count($values) === 1;
            $condition = $this->_getConditionSql(
                $conditionColumn,
                [$isSingleValue ? 'eq' : 'in' => $isSingleValue ? reset($values) : $values]
            );
            $select->where($condition);

            if (!$isSingleValue) {
                $select->group($this->_getMappedField(LabelInterface::LABEL_ID));
            }
        }
    }

    public function addIsNewFilterApplied(): void
    {
        $isNewCondition = $this->_getConditionSql(
            LabelInterface::CONDITION_SERIALIZED,
            ['like' => '%Condition\\\\\\\\IsNew%']
        );
        $this->getSelect()->where($isNewCondition);
    }

    public function addIsSaleFilterApplied(): void
    {
        $isNewCondition = $this->_getConditionSql(
            LabelInterface::CONDITION_SERIALIZED,
            ['like' => '%Condition\\\\\\\\OnSale%']
        );
        $this->getSelect()->where($isNewCondition);
    }

    public function filterByQtyCondition(): void
    {
        $qtyCondition = $this->_getConditionSql(
            LabelInterface::CONDITION_SERIALIZED,
            ['like' => '%Condition\\\\\\\\Qty%']
        );
        $this->getSelect()->where($qtyCondition);
    }

    public function getItemById($idValue): ?LabelInterface
    {
        $this->load();
        /** @var ?LabelInterface $label **/
        $label = $this->_items[$idValue] ?? null;

        return $label;
    }

    /**
     * Sets a value indicating what type of frontend settings to load.
     * Possible values 1 (product list) 2 (product page)
     *
     * @param int $mode
     */
    public function setMode(int $mode): void
    {
        $mode = $mode === self::MODE_LIST ? self::MODE_LIST : self::MODE_PDP;
        $this->currentMode = $mode;
    }

    public function getMode(): ?int
    {
        return $this->currentMode;
    }

    /**
     * Find product attribute in conditions
     */
    public function addAttributeInConditionFilter(string $attributeCode): self
    {
        $match = sprintf('%%%s%%', substr($this->serializer->serialize(['attribute' => $attributeCode]), 1, -1));
        /**
         * Information about conditions and actions stored in table as JSON encoded array
         * in conditions_serialized field.
         * If you want to find rules that contains some particular attribute, the easiest way to do so is serialize
         * attribute code in the same way as it stored in the serialized columns and execute SQL search
         * with like condition.
         */
        $this->addFieldToFilter('conditions_serialized', ['like' => $match]);

        return $this;
    }
}
