<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Plugin\PageBuilder\Model;

class ConfigPlugin
{
    public const AMMEGAMENU_PRODUCT_SLIDER = 'ammegamenu_product_slider';

    private $magentoVersion;

    public function __construct(\Amasty\Base\Model\MagentoVersion $magentoVersion)
    {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param \Magento\PageBuilder\Model\Config $subject
     * @param array $types
     * @return array
     */
    public function afterGetContentTypes(\Magento\PageBuilder\Model\Config $subject, array $types)
    {
        if (version_compare($this->magentoVersion->get(), '2.3.4', '>=')) {
            foreach ($types as $key => &$type) {
                if ($key == self::AMMEGAMENU_PRODUCT_SLIDER) {
                    $type['menu_section'] = '';
                    break;
                }
            }
        }

        return $types;
    }
}
