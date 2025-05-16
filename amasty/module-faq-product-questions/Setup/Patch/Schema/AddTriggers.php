<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Schema;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Api\Data\VisitStatInterface;
use Amasty\Faq\Model\ResourceModel\Category as CategoryResource;
use Amasty\Faq\Model\ResourceModel\Question as QuestionResource;
use Amasty\Faq\Model\ResourceModel\VisitStat as VisitStatResource;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Ddl\TriggerFactory;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddTriggers implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @var TriggerFactory
     */
    private $triggerFactory;

    public function __construct(
        SchemaSetupInterface $schemaSetup,
        TriggerFactory $triggerFactory
    ) {
        $this->schemaSetup = $schemaSetup;
        $this->triggerFactory = $triggerFactory;
    }

    public function apply()
    {
        $connection = $this->schemaSetup->getConnection();

        /** @var Trigger $trigger */
        $trigger = $this->triggerFactory->create()
            ->setName('update_visit_stat')
            ->setTime(Trigger::TIME_AFTER)
            ->setEvent(Trigger::EVENT_INSERT)
            ->setTable($this->schemaSetup->getTable(VisitStatResource::TABLE_NAME));

        $trigger->addStatement($this->getVisitStatStatement());

        $connection->dropTrigger($trigger->getName());
        $connection->createTrigger($trigger);
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    private function getVisitStatStatement()
    {
        $categoryTableTrigger = sprintf(
            "UPDATE %s SET %s = %s + 1 WHERE %s = NEW.%s",
            $this->schemaSetup->getTable(CategoryResource::TABLE_NAME),
            CategoryInterface::VISIT_COUNT,
            CategoryInterface::VISIT_COUNT,
            CategoryInterface::CATEGORY_ID,
            VisitStatInterface::CATEGORY_ID
        );

        $questionTableTrigger = sprintf(
            "UPDATE %s SET %s = %s + 1 WHERE %s = NEW.%s",
            $this->schemaSetup->getTable(QuestionResource::TABLE_NAME),
            QuestionInterface::VISIT_COUNT,
            QuestionInterface::VISIT_COUNT,
            QuestionInterface::QUESTION_ID,
            VisitStatInterface::QUESTION_ID
        );

        return $categoryTableTrigger . ';' . $questionTableTrigger;
    }
}
