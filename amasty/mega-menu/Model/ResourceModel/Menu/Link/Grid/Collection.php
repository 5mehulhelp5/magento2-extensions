<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Link\Grid;

use Amasty\MegaMenu\Model\ResourceModel\Menu\ResourceResolver;
use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\WrapColumns;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Grid\Collection as GridCollectionLite;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class Collection extends GridCollectionLite
{
    /**
     * @var array
     */
    private $mappedFields = [
        'entity_id' => 'main_table.entity_id'
    ];

    /**
     * @var ResourceResolver
     */
    private $resourceResolver;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        ResourceResolver $resourceResolver,
        WrapColumns $wrapColumns,
        $model = Document::class,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $eventPrefix,
            $eventObject,
            $resourceModel,
            $wrapColumns,
            $model,
            $connection,
            $resource
        );
        $this->resourceResolver = $resourceResolver;
    }

    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        $this->resourceResolver->joinLinkByStore(
            $this,
            'main_table',
            'link',
            $this->getResource()->getTable(ItemInterface::TABLE_NAME),
            Store::DEFAULT_STORE_ID
        );
    }
}
