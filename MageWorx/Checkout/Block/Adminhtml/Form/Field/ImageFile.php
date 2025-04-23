<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class ImageFile extends AbstractBlock
{
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    /**
     * Set element's HTML ID
     *
     * @param string $elementId ID
     * @return $this
     */
    public function setId(string $elementId = ''): self
    {
        $this->setData('id', $elementId);

        return $this;
    }

    /**
     * Set element's CSS class
     *
     * @param string $class Class
     * @return $this
     */
    public function setClass(string $class = ''): self
    {
        $this->setData('class', $class);

        return $this;
    }

    /**
     * Set element's HTML title
     *
     * @param string $title Title
     * @return $this
     */
    public function setTitle(string $title = ''): self
    {
        $this->setData('title', $title);

        return $this;
    }

    /**
     * HTML ID of the element
     *
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->getData('id');
    }

    /**
     * CSS class of the element
     *
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->getData('class');
    }

    /**
     * Returns HTML title of the element
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData('title');
    }

    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        $html = '';

        $name = $this->getName() ?
            'name="' . /** @noEscape */
            $this->getName() . '" ' :
            ' ';

        $id = $this->getInputId() ?
            'id="' . /** @noEscape */
            $this->getInputId() . '" ' :
            ' ';

        $class = $this->getClass() ?
            'class="' . $this->escapeHtmlAttr($this->getClass()) . '" ' :
            ' ';

        $title = $this->getTitle() ?
            'title="' . $this->escapeHtmlAttr($this->getTitle()) . '" ' :
            ' ';

        $src = $this->getValue() ? 'src="' . /** @noEscape */
            $this->getValue() . '" ' : ' ';

        $html .= '<span id="' . /** @noEscape */
            $this->getInputId() . '_placeholder' . '" class="image-placeholder">'
            . $this->escapeUrl($this->getValue())
            . '<\/span>';

        $html .= '<input type="hidden" id="' . /** @noEscape */
            $this->getInputId() . '_old' . '" class="image-old" '
            . 'name="' . /** @noEscape */
            $this->getName() . '[old]" '
            . 'value="' . $this->escapeUrl($this->getValue()) . '" '
            . '\/>';

        $html .= '<input ' .
            'type="file" ' .
            'value="" ' .
            $name .
            $id .
            $class .
            $title .
            $this->getExtraParams() .
            $src .
            ' \/>';

        return $html;
    }

    /**
     * Alias for toHtml()
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->toHtml();
    }
}
