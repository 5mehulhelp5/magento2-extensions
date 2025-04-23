<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\InstagramFeedPro\Observer\Frontend\Core;


use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\Config\Source\ProductPagePos;

class LayoutRenderElement implements ObserverInterface
{
    const XML_PATH_ENABLE = 'general/enabled';
    const XML_PATH_ENABLE2 = 'product_page/show_content_custom';
    const XML_PATH_POSITION = 'product_page/custom_content_position';
    const XML_PATH_CONTENT = 'product_page/content_custom';
    const XML_PATH_CONTENT2 = 'product_page/content_custom2';
    const STR_HIDE_CONTENT = 'data-oxinst-hide';
	const XML_PATH_TWOCOLS = 'product_page/two_columns';
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var LayoutInterface
     */
    protected $layout;
    /**
     * @var string
     */
    protected $_content;
    /**
     * @var array
     */
    protected $_position;
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * LayoutRenderElement constructor.
     * @param RequestInterface $request
     * @param Helper $helper
     */
    public function __construct(
        RequestInterface $request,
        Helper $helper
    ) {
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (in_array($this->request->getFullActionName(), ['catalog_product_view'])
            && $this->isEnabled()
            && $this->getContent()
        ) {
            /** @var LayoutInterface $layout */
            $layout = $observer->getLayout();
            $position = $this->getPosition($layout);
            if (!empty($position)) {
                [$blockName, $after] = $position;
                $element_name = $observer->getElementName();
                if (!empty($blockName) && $element_name === $blockName) {
                    /** @var DataObject $transport */
                    $transport = $observer->getData('transport');
                    $html = $transport->getData('output');
                    if ($after) {
                        $html = $html . $this->getContent(true);
                    } else {
                        $html = $this->getContent(true) . $html;
                    }

                    $transport->setData('output', $html);
                }
            }
        }
    }

    /**
     * @return bool
     */
    protected function isEnabled()
    {
        return $this->helper->getModuleConfig(static::XML_PATH_ENABLE)
            && $this->helper->getModuleConfig(static::XML_PATH_ENABLE2);
    }

    /**
     * @param bool $transform
     * @return string
     */
    protected function getContent()
    {
        if (null === $this->_content) {
            $content = (string)$this->helper->getModuleConfig(static::XML_PATH_CONTENT);
            $content2 = (string)$this->helper->getModuleConfig(static::XML_PATH_CONTENT2);
            $content = preg_replace('#<p>(\s*)</p>#i', '', $content);
            $content2 = preg_replace('#<p>(\s*)</p>#i', '', $content2);
            $content = $this->helper->getBlockTemplateProcessor($content);
            $content2 = $this->helper->getBlockTemplateProcessor($content2);
			$classes = '';
			$twocols = $this->helper->getModuleConfig(static::XML_PATH_TWOCOLS);
			if( !$twocols){
				$classes .= '-one-col';
			}
            if (false === strpos((string)$content, static::STR_HIDE_CONTENT)
                && false === strpos((string)$content2, static::STR_HIDE_CONTENT)
            ) {
                if (!empty($content)) {
                    $content = '<div class="ox-product-block__col1">' . $content . '</div>';
                } else {
                    $content = '';
                }
                if ($twocols && !empty($content2)) {
                    $content2 = '<div class="ox-product-block__col2">' . $content2 . '</div>';
                } else {
                    $content2 = '';
                }
            } else {
                $content = '';
                $content2 = '';
            }

            if ($content || $content2) {
                $this->_content = '<div class="ox-product-block '. $classes .'"><div class="ox-row">' . $content . $content2 . '</div></div>';
            } else {
                $this->_content = '';
            }
        }

        return $this->_content;
    }

    /**
     * @param LayoutInterface $layout
     * @return array
     */
    protected function getPosition($layout)
    {
        if (null === $this->_position) {
            $position = $this->helper->getModuleConfig(static::XML_PATH_POSITION);
            $after = null;
            $_position = null;
            if (false !== strpos((string)$position, ProductPagePos::STR_BEFORE)) {
                $after = false;
                $_position = explode(ProductPagePos::STR_BEFORE, $position);
            } elseif (false !== strpos((string)$position, ProductPagePos::STR_AFTER)) {
                $after = true;
                $_position = explode(ProductPagePos::STR_AFTER, $position);
            }

            if (null !== $after) {
                [$parentBlockName, $blockName] = $_position;
                if (!empty($parentBlockName)) {
                    if ($layout->hasElement($parentBlockName)) {
                        $childNames = $layout->getChildNames($parentBlockName);
                        if (empty($blockName)) {
                            $blockName = $after ? array_pop($childNames) : array_shift($childNames);
                            if (!empty($blockName)) {
                                $this->_position = [$blockName, $after];
                            } else {
                                $this->_position = [];
                            }
                        } else {
                            if (!empty($childNames) && in_array($blockName, $childNames)) {
                                $this->_position = [$blockName, $after];
                            } else {
                                $this->_position = [];
                            }
                        }
                    }
                } elseif (!empty($blockName)) {
                    if ($layout->hasElement($blockName)) {
                        $this->_position = [$blockName, $after];
                    }
                }
            } else {
                $this->_position = [];
            }
        }

        return $this->_position;
    }
}
