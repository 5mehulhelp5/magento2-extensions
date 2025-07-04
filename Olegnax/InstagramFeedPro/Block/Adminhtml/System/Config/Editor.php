<?php

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\System\Config;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Editor extends \Magento\Config\Block\System\Config\Form\Field {

	/**
	 * @var  Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param Context       $context
	 * @param WysiwygConfig $wysiwygConfig
	 * @param array         $data
	 */
	public function __construct(
	Context $context, WysiwygConfig $wysiwygConfig, array $data = []
	) {
		$this->_wysiwygConfig = $wysiwygConfig;
		parent::__construct( $context, $data );
	}

	protected function _getElementHtml( \Magento\Framework\Data\Form\Element\AbstractElement $element ) {
		$element->setWysiwyg( true );
		$config = $this->_wysiwygConfig->getConfig( $element );
		$element->setConfig( $config );
		return parent::_getElementHtml( $element );
	}

}
