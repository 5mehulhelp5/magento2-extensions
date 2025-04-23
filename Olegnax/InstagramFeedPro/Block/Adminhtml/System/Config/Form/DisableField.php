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

class DisableField extends Field
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
     * @throws Exception
     */
    public function render(AbstractElement $element)
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

        if (!$status || $devLicense) {
            /** @noinspection PhpUndefinedMethodInspection */
            $element->setDisabled(true);
            $element->setReadonly(true);
            $element->setValue(0);
        }

        return parent::render($element);
    }
}
