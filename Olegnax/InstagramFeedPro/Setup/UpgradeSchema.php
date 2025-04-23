<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\InstagramFeedPro\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

use Magento\Framework\Encryption\EncryptorInterface;
use Zend_Db_Exception;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var EncryptorInterface $encryptor
     */
    protected $encryptor;
    /**
     * UpgradeSchema constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor)
    {
        $this->encryptor = $encryptor;
    }
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $this->applyUpgradeFunctions($setup, $context);
        $this->resetUIOrder($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    private function applyUpgradeFunctions(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $methods = $this->getUpgradeFunctions($context->getVersion());
        foreach ($methods as $method) {
            call_user_func_array([$this, $method], [$setup, $context]);
        }
    }

    /**
     * @param string $low_version
     * @return array
     */
    private function getUpgradeFunctions($low_version = '1.0.0')
    {
        $methods = get_class_methods($this);
        foreach ($methods as $key => $method) {
            if (preg_match('/^upgrade_(.+)$/i', $method, $matches)) {
                if (version_compare($low_version, $matches[1], '<')) {
                    continue;
                }
            }
            unset($methods[$key]);
        }

        $methods = array_filter($methods);
        $methods = array_unique($methods);
        sort($methods);

        return $methods;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade_1_1_0(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $tables = [
            'olegnax_instagramfeedpro_intspost_hotspot' => [
                'image_index' => [
                    'type' => Table::TYPE_TEXT,
                    'options' => [
                        Table::OPTION_NULLABLE => true,
                    ],
                ],
            ],
            'olegnax_instagramfeedpro_intspost_product_entity' => [
                'image_index' => [
                    'type' => Table::TYPE_TEXT,
                    'options' => [
                        Table::OPTION_NULLABLE => true,
                    ],
                ],
            ],
        ];
        $connection = $setup->getConnection();

        foreach ($tables as $table_name => $table) {
            if (!$setup->tableExists($table_name)) {
                continue;
            }
            $_table = $setup->getTable($table_name);
            foreach ($table as $field_name => $field) {
                $_field = array_replace(
                    [
                        'type' => Table::TYPE_INTEGER,
                        'size' => null,
                        'comment' => 'Added by plugin',
                    ],
                    $field
                );

                $connection->addColumn($_table, $field_name, $_field);
            }
        }
    }
        /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade_1_1_5_0(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->updateTokenValues($setup);
    }

    protected function updateTokenValues(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable('olegnax_instagramfeedpro_intsuser');

        $select = $connection->select()->from($tableName, ['user_id', 'access_token']);
        $users = $connection->fetchAll($select);
        if(is_array($users) && count($users)){
            foreach ($users as $user) {
                $token = $user['access_token'];
                if(!empty($token)){
                    $data = ['access_token' => $this->encryptor->encrypt($token)];
                    $whereCondition = ['user_id = ?' => $user['user_id']];
                    $connection->update($tableName, $data, $whereCondition);
                }
            }
        }
    }
    protected function resetUIOrder(SchemaSetupInterface $setup)
    {
        $gridIdentifier = 'olegnax_instagramfeedpro_intsuser_listing';
        try {
            $connection = $setup->getConnection();
            $connection->delete(
                $setup->getTable('ui_bookmark'),
                ['namespace = ?' => $gridIdentifier]
            );
        } catch (\Exception $e) {
            return;
        }
    }
}
