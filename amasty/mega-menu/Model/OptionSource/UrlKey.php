<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\OptionSource;

use Amasty\MegaMenuLite\Model\OptionSource\UrlKey as UrlKeyLite;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class UrlKey extends UrlKeyLite implements OptionSourceInterface
{
    public const CMS_PAGE = 2;

    public const LANDING_PAGE = 3;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => self::CMS_PAGE, 'label' => __('CMS Page')],
            $this->getLandingOption()
        ];

        return array_merge(parent::toOptionArray(), $options);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLabelByValue($value)
    {
        foreach ($this->toOptionArray() as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }

        return '';
    }

    /**
     * @return array
     */
    private function getLandingOption()
    {
        $result = [
            'value' => self::LANDING_PAGE
        ];
        $landingLabel = __('Amasty Landing Page');
        if (!$this->moduleManager->isEnabled('Amasty_Xlanding')) {
            $landingLabel .= sprintf(' (%s)', __('Not installed'));
            $result['disabled'] = true;
        }
        $result['label'] = $landingLabel;

        return $result;
    }

    /**
     * @return array
     */
    public function getTablesToJoin()
    {
        $tables = [self::CMS_PAGE => 'cms_page'];
        if ($this->moduleManager->isEnabled('Amasty_Xlanding')) {
            $tables[] = 'amasty_xlanding_page';
        }

        return $tables;
    }
}
