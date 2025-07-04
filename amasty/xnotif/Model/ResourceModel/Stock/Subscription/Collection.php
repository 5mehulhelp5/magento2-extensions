<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\ResourceModel\Stock\Subscription;

use Amasty\Xnotif\Model\ResourceModel\Stock\OrderTotals;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\ProductAlert\Model\ResourceModel\Stock\Collection as StockCollection;
use Psr\Log\LoggerInterface;

class Collection extends StockCollection
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var string
     */
    protected $productIdLink;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->attributeRepository = $attributeRepository;
        $this->productIdLink = $productResource->getLinkField();
    }

    /**
     * @return string
     */
    public function getIdFieldName()
    {
        $idFieldName = parent::getIdFieldName();
        if (!$idFieldName) {
            $idFieldName = 'alert_stock_id';
            $this->_setIdFieldName($idFieldName);
        }

        return $idFieldName;
    }

    /**
     * @return $this
     */
    public function _renderFiltersBefore()
    {
        $nameAttribute = $this->attributeRepository->get(Product::ENTITY, 'name');
        $productVarcharTable = $this->getTable('catalog_product_entity_varchar');

        $this->getSelect()
            ->join(
                ['product' => $this->getTable('catalog_product_entity')],
                'product.entity_id = main_table.product_id',
                ['product_sku' => 'sku']
            )
            ->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'customer.entity_id = main_table.customer_id',
                ['last_name' => 'lastname', 'first_name' => 'firstname']
            )
            ->joinLeft(
                ['product_name_by_store' => $productVarcharTable],
                sprintf('product.%s = product_name_by_store.%s', $this->productIdLink, $this->productIdLink)
                . ' AND product_name_by_store.attribute_id = '
                . $nameAttribute->getAttributeId() . ' AND ' . $this->getStoreIdColumn()
                . ' = product_name_by_store.store_id',
                ['value']
            )
            ->joinLeft(
                ['product_name_default' => $productVarcharTable],
                sprintf('product.%s = product_name_default.%s', $this->productIdLink, $this->productIdLink)
                . ' AND product_name_default.attribute_id = '
                . $nameAttribute->getAttributeId() . ' AND product_name_default.store_id = 0',
                ['value']
            );

        $this->updateCustomerFields();

        parent::_renderFiltersBefore();

        return $this;
    }

    /**
     * Check if field exist in alert table; else get from customer table
     */
    private function updateCustomerFields()
    {
        $columnsPart = $this->getSelect()->getPart('columns');

        $email = new \Zend_Db_Expr('IF (main_table.email IS NOT NULL, main_table.email, customer.email)');
        $productName = new \Zend_Db_Expr(
            'IF (product_name_by_store.value IS NOT NULL, product_name_by_store.value, product_name_default.value)'
        );

        $columnsPart[] = [
            'main_table',
            $this->getStoreIdColumn(),
            'store_id'
        ];
        $columnsPart[] = [
            'main_table',
            $email,
            'email'
        ];
        $columnsPart[] = [
            'main_table',
            $productName,
            'product_name'
        ];

        $this->getSelect()->reset(Select::COLUMNS)->setPart('columns', $columnsPart);
    }

    /**
     * @return \Zend_Db_Expr
     */
    private function getStoreIdColumn()
    {
        return new \Zend_Db_Expr('IF (main_table.store_id IS NOT NULL, main_table.store_id, customer.store_id)');
    }

    /**
     * @deprecated moved to resource model.
     * @see OrderTotals::getTotals
     *
     * @param string $date
     * @return string
     */
    public function getTotals($date)
    {
        return ObjectManager::getInstance()->get(OrderTotals::class)->getTotals($date);
    }

    /**
     * @deprecated moved to resource model.
     * @see OrderTotals::joinSales
     *
     * @param bool $daily
     * @return $this
     */
    public function joinSales($daily = true)
    {
        ObjectManager::getInstance()->get(OrderTotals::class)->joinSales($this->getSelect(), $daily);

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function applyMostWanted($limit)
    {
        $this->getSelect()
            ->columns('count(`product_id`) AS wanted_count')
            ->group('product_id')
            ->order('count(`product_id`) DESC')
            ->limit($limit);

        return $this;
    }
}
