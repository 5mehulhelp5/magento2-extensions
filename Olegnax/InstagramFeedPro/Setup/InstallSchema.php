<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Setup;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;
use Zend_Db_Expr;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->_install($setup);
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function _install(SchemaSetupInterface $setup)
    {
        $tables = $this->tables();
        foreach ($tables as $tableName => $tableFields) {
            if ($setup->tableExists($tableName)) {
                continue;
            }
            /** @var Table $table */
            $table = $setup->getConnection()->newTable($setup->getTable($tableName));
            $index = [];
            foreach ($tableFields as $fieldName => $field) {
                if (!array_key_exists('type', $field)) {
                    $field['type'] = Table::TYPE_INTEGER;
                }
                if (!array_key_exists('size', $field)) {
                    $field['size'] = null;
                }
                if (!array_key_exists('options', $field) || empty($field['options'])) {
                    $field['options'] = [
                    ];
                }
                if (!array_key_exists(Table::OPTION_NULLABLE, $field['options'])) {
                    $field['options'][Table::OPTION_NULLABLE] = false;
                }

                if (!array_key_exists('comment', $field)) {
                    $field['comment'] = null;
                }

                $table->addColumn($fieldName, $field['type'], $field['size'], $field['options'], $field['comment']);
                if (array_key_exists('foreign', $field)) {
                    $foreign = $field['foreign'];
                    if (is_string($foreign)) {
                        $foreign = [
                            'table' => $setup->getTable($foreign),
                        ];
                    }
                    if (!array_key_exists('column', $foreign)) {
                        $foreign['column'] = $fieldName;
                    }
                    if (!array_key_exists('name', $foreign)) {
                        $foreign['name'] = 'OFK_' . mb_strtoupper(
                                md5(
                                    implode(
                                        '_',
                                        [
                                            $tableName,
                                            $fieldName,
                                            $foreign['table'],
                                            $foreign['column'],
                                        ]
                                    )
                                )
                            );
                    }
                    if (!array_key_exists('onDelete', $foreign)) {
                        $foreign['onDelete'] = AdapterInterface::FK_ACTION_CASCADE;
                    }
                    $table->addForeignKey(
                        $foreign['name'],
                        $fieldName,
                        $foreign['table'],
                        $foreign['column'],
                        $foreign['onDelete']
                    );
                }
                if (array_key_exists('index', $field)) {
                    $_index = $field['index'];
                    if (is_bool($_index)) {
                        $_index = mb_strtoupper($tableName . '_' . $fieldName);
                    }
                    $index[$_index][] = $fieldName;
                }
            }
            if (!empty($index)) {
                foreach ($index as $indexName => $fields) {
                    $table->addIndex(
                        $indexName,
                        $fields
                    );
                }
            }

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @return array
     */
    private function tables()
    {
        $smallIntDefault1 = [
            'type' => Table::TYPE_SMALLINT,
            'options' => [
                Table::OPTION_DEFAULT => 1,
            ],
        ];
        $text = [
            'type' => Table::TYPE_TEXT,
        ];
        $textNullable255 = [
            'type' => Table::TYPE_TEXT,
            'size' => 255,
            'options' => [
                Table::OPTION_NULLABLE => true,
            ],
        ];
        $textNullable = [
            'type' => Table::TYPE_TEXT,
            'options' => [
                Table::OPTION_NULLABLE => true,
            ],
        ];
        $dateTimeTimestamp = [
            'type' => Table::TYPE_DATETIME,
            'options' => [
                Table::OPTION_DEFAULT => new Zend_Db_Expr('CURRENT_TIMESTAMP'),
            ],
        ];
        return [
            'olegnax_instagramfeedpro_intspost' => [
                'intspost_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => true,
                        Table::OPTION_PRIMARY => true,
                    ],
                ],
                'ints_id' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 20,
                ],
                'owner' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 20,
                ],
                'media_type' => $textNullable255,
                'shortcode' => $textNullable255,
                'code' => $text,
                'caption' => $text,
                'media_url' => $text,
                'thumbnail_url' => $text,
                'children' => $text,
                'dimensions_width' => [
                    'options' => [
                        Table::OPTION_NULLABLE => true,
                    ],
                ],
                'dimensions_height' => [
                    'options' => [
                        Table::OPTION_NULLABLE => true,
                    ],
                ],
                'comments_count' => [
                    'options' => [
                        Table::OPTION_DEFAULT => 0,
                    ],
                ],
                'like_count' => [
                    'options' => [
                        Table::OPTION_DEFAULT => 0,
                    ],
                ],
                'is_active' => $smallIntDefault1,
                'timestamp' => $dateTimeTimestamp,
            ],
            'olegnax_instagramfeedpro_intsuser' => [
                'intsuser_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => true,
                        Table::OPTION_PRIMARY => true,
                    ],
                ],
                'user_id' => [
                    'type' => Table::TYPE_TEXT,
                    'size' => 20,
                ],
                'username' => $textNullable,
                'profile_picture' => $textNullable,
                'account_type' => $textNullable,
                'access_token' => $textNullable,
                'expire' => [
                    'options' => [
                        Table::OPTION_NULLABLE => true,
                    ],
                ],
                'is_active' => $smallIntDefault1,
                'media_count' => [
                    'options' => [
                        Table::OPTION_DEFAULT => 0,
                    ],
                ],
                'timestamp' => $dateTimeTimestamp,
            ],
            'olegnax_instagramfeedpro_hotspot' => [
                'hotspot_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => true,
                        Table::OPTION_PRIMARY => true,
                    ],
                ],
                'name' => $text,
                'status' => $smallIntDefault1,
                'marker_style' => $text,
                'hotspot_text_icon' => $text,
                'hotspot_text' => $text,
                'content' => $text,
                'hotspot_width' => $textNullable255,
                'hotspot_height' => $textNullable255,
                'hotspot_color' => $textNullable255,
                'hotspot_bg' => $textNullable255,
                'hotspot_pulse' => $text,
                'hotspot_pulse_color' => $textNullable255,
                'hotspot_shadow' => $text,
                'hotspot_shadow_color' => $text,
                'hotspot_radius' => $textNullable255,
                'hotspot_mobile' => $textNullable255,
                'hotspot_custom_class' => $text,
                'tooltip_width' => $textNullable255,
                'tooltip_border_radius' => $textNullable255,
                'tooltip_text_color' => $textNullable255,
                'tooltip_bg_color' => $textNullable255,
                'tooltip_shadow_color' => $textNullable255,
            ],
            'olegnax_instagramfeedpro_intspost_store' => [
                'intspost_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'foreign' => 'olegnax_instagramfeedpro_intspost',
                ],
                'store_id' => [
                    'type' => Table::TYPE_SMALLINT,
                    'options' => [
                        Table::OPTION_UNSIGNED => true,
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'index' => true,
                    'foreign' => 'store',
                ],
            ],
            'olegnax_instagramfeedpro_intspost_hotspot' => [
                'intspost_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'foreign' => 'olegnax_instagramfeedpro_intspost',
                ],
                'hotspot_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'index' => true,
                    'foreign' => 'olegnax_instagramfeedpro_hotspot',
                ],
                'position' => [
                    'options' => [
                        Table::OPTION_DEFAULT => 0,
                    ],
                ],
                'position_top' => $textNullable,
                'position_left' => $textNullable,
                'image_index' => $textNullable,

            ],
            'olegnax_instagramfeedpro_intspost_product_entity' => [
                'intspost_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'foreign' => 'olegnax_instagramfeedpro_intspost',
                ],
                'entity_id' => [
                    'options' => [
                        Table::OPTION_IDENTITY => false,
                        Table::OPTION_UNSIGNED => true,
                        Table::OPTION_PRIMARY => true,
                    ],
                    'index' => true,
                    'foreign' => 'catalog_product_entity',
                ],
                'position' => [
                    'options' => [
                        Table::OPTION_DEFAULT => 0,
                    ],
                ],
                'position_top' => $textNullable,
                'position_left' => $textNullable,
                'image_index' => $textNullable,
                'marker_style' => $textNullable,
            ],
        ];
    }
}
