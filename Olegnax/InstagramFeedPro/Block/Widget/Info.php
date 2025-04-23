<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @method string getOwner()
 * @method string getMediaType()
 * @method string getShowPager()
 * @method string getLazyLoad()
 * @method string getTemplate()
 * @method string getImageResize()
 * @method string getCacheLifetime()
 * @method string getCheckRelated()
 * @method string getDateFormat()
 */
class Info extends Template implements BlockInterface
{
    /**
     * @var Factory
     */
    protected $_elementFactory;

    /**
     * Info constructor.
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
		$divider = '<div class="ox-info-block">';
		$divider .= '<a href="https://olegnax.com/" target="_blank" class="ox-info-block__logo"></a>';
		$divider .= '<div class="ox-info-block__docs"><a href="https://olegnax.com/documentation/instagram-feed-pro-extension-for-magento-2/" target="_blank">' . __('Extension Docs') .  '</a></div>';
		$divider .= '</div>';
		$element->setData( 'after_element_html', $divider );
        return $element;
    }

}
