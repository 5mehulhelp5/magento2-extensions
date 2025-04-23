<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ResourceModel\Menu;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position\UpdateChildPosition;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Link extends AbstractDb
{
    /**
     * @var UpdateChildPosition
     */
    private $updateChildPosition;

    public function __construct(
        UpdateChildPosition $updateChildPosition,
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->updateChildPosition = $updateChildPosition;
    }

    /**
     * Initialize table nad PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LinkInterface::TABLE_NAME, LinkInterface::ENTITY_ID);
    }

    public function isEntityExist(int $entityId): bool
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable())
            ->where(sprintf('%s = ?', LinkInterface::ENTITY_ID), $entityId);

        return (bool) $this->getConnection()->fetchRow($select);
    }

    /**
     * @param LinkInterface $object
     * @return Link
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->updateChildPosition->execute($object);

        return parent::_afterSave($object);
    }
}
