<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup;

use Amasty\Faq\Model\ResourceModel\Category;
use Amasty\Faq\Model\ResourceModel\Question;
use Amasty\Faq\Model\ResourceModel\Tag;
use Amasty\Faq\Model\ResourceModel\VisitStat;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->uninstallTables($setup)
            ->uninstallConfigData($setup)
            ->removeEmailTemplates($setup);
        $setup->endSetup();
    }

    private function uninstallTables(SchemaSetupInterface $setup): self
    {
        $tablesToDrop = [
            Category::CUSTOMER_GROUPS_LINK_TABLE_NAME,
            Question::CUSTOMER_GROUPS_LINK_TABLE_NAME,
            Question::PRODUCT_CATEGORY_LINK_TABLE_NAME,
            VisitStat::TABLE_NAME,
            Category::STORE_LINK_TABLE_NAME,
            Question::PRODUCT_LINK_TABLE_NAME,
            Question::TAG_LINK_TABLE_NAME,
            Question::STORE_LINK_TABLE_NAME,
            Question::CATEGORY_LINK_TABLE_NAME,
            Tag::TABLE_NAME,
            Category::TABLE_NAME,
            Question::TABLE_NAME
        ];
        foreach ($tablesToDrop as $table) {
            $setup->getConnection()->dropTable(
                $setup->getTable($table)
            );
        }

        return $this;
    }

    private function uninstallConfigData(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            "`path` LIKE 'amastyfaq%'"
        );

        return $this;
    }

    private function removeEmailTemplates(SchemaSetupInterface $setup): self
    {
        $setup->getConnection()->delete(
            $setup->getTable('email_template'),
            '`orig_template_code` LIKE \'amastyfaq_%\''
        );

        return $this;
    }
}
