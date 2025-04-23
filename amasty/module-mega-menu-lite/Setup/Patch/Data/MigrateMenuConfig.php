<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Setup\Patch\Data;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MigrateMenuConfig implements DataPatchInterface
{
    public const CONFIG_PATH = [
        'ammegamenu/color/menu_background' => 'ammegamenu/color/main_menu_background',
        'ammegamenu/color/menu_highlight' => 'ammegamenu/color/main_menu_text_hover',
        'ammegamenu/color/menu_text' => 'ammegamenu/color/main_menu_text',
        'ammegamenu/color/submenu_background' => 'ammegamenu/color/submenu_background_color',
        'ammegamenu/color/category_hover_color' => 'ammegamenu/color/submenu_text_hover',
    ];

    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    public function __construct(
        ConfigInterface $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function apply()
    {
        $connection = $this->resourceConfig->getConnection();
        foreach (self::CONFIG_PATH as $oldConfigPath => $newConfigPath) {
            $select = $connection->select()
                ->from($this->resourceConfig->getMainTable())
                ->where('path = ?', $newConfigPath);
            if ($connection->fetchOne($select) === false) {
                $connection->update(
                    $this->resourceConfig->getMainTable(),
                    ['path' => $newConfigPath],
                    ['path = ?' => $oldConfigPath]
                );
            }
        }

        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
