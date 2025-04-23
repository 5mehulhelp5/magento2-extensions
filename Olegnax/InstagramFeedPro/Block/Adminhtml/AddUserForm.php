<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Block\Adminhtml;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context as TemplateContext;

class AddUserForm extends Template
{
    protected $_template = 'addform.phtml';
    /**
     * @param TemplateContext $context
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

}
