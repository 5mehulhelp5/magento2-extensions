<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Seo Rich Data Yotpo (System)
*/

declare(strict_types=1);

namespace Amasty\SeoRichDataYotpo\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class YotpoReviews extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element): string
    {
        $element->setComment(sprintf(
            'Please make sure to generate an access token in Yotpo Compatibility <a href="%s">settings</a>',
            $this->getYotpoSettingsUrl()
        ));

        return parent::_renderValue($element);
    }

    /**
     * @return string
     */
    private function getYotpoSettingsUrl(): string
    {
        return $this->getUrl(
            'adminhtml/system_config/edit',
            ['section' => 'amasty_yotpo']
        );
    }
}
