<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\System\Config\Form;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Olegnax\InstagramFeedPro\Helper\Helper;

class InfoField extends Field
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * InfoField constructor.
     * @param Context $context
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return $this->_decorateRowHtml($element, $this->_renderValue($element));
    }

    /**
     * Render element value
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element)
    {
        $license = null;
        try {
            $license = $this->_helper->get();
        } catch (Exception $exception) {
            $license = null;
        }
        $status = !empty($license)
            && isset($license->data->the_key)
            && $license->data->the_key == $this->_helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code')
            && $license->data->status == "active";
        $devLicense = $status && isset($license->notices->develop);
        $supportExpired = $status && $license->data->has_expired;

        $html = '<td class="value" colspan="3">';
        if (!$status) {
            $html .= '<div class="message message-error error"><span>' . __('Extension is not activated! Sync is disabled.') . ' <a href="' . $this->getLicenseUrl() . '">' . __('Click here to Activate') . '</a> </span></div>';
        } elseif ($devLicense) {
            $html .= '<div class="message message-error error"><span>' . __('Extension is not activated! Sync is disabled.') . ' <a href="' . $this->getLicenseUrl() . '">' . __('Click here to Activate') . '</a></span></div>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * @return string
     */
    protected function getLicenseUrl()
    {
        return $this->getUrl('*/*/*', ['_current' => true, 'section' => 'olegnax_instagram_license']);
    }
}
