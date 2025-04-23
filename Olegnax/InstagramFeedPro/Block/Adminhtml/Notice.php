<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Block\Adminhtml;

use Exception;
use Magento\Framework\View\Element\Template;
use Olegnax\InstagramFeedPro\Helper\Helper;

class Notice extends Template
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Notice constructor.
     * @param Template\Context $context
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Helper $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function _toHtml()
    {
        $license = $this->_helper->get();
        $notice = '';
        if (empty($license)) {
            $notice = '<div class="message message-error error" style="margin-bottom: 20px;"><span>' . __('Extensions is not activated!') . '</span></div>';
        }
        return $notice;
    }

    /**
     * @return string
     */
    protected function getLicenseUrl()
    {
        return $this->getUrl('admin/system_config/edit', ['_current' => true, 'section' => 'olegnax_instagram_license']);
    }
}
