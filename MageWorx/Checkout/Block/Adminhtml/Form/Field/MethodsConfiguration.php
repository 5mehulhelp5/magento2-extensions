<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use MageWorx\Checkout\Helper\PaymentMethods as Helper;

class MethodsConfiguration extends AbstractFieldArray
{
    /**
     * @var Methods
     */
    protected $methodsRenderer;

    /**
     * @var ImageFile
     */
    protected $imageFileRenderer;

    /**
     * @var string
     */
    protected $_template = 'MageWorx_Checkout::system/config/form/field/methods_configuration_array.phtml';

    /**
     * @var StoreInterface
     */
    protected $store;

    /**
     * Prepare to render
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'methods_id',
            [
                'label'    => __('Payment Method'),
                'renderer' => $this->getMethodsRenderer()
            ]
        );

        $this->addColumn(
            'image',
            [
                'label'    => __('Image'),
                'renderer' => $this->getImageRenderer()
            ]
        );

        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName): string
    {
        if (empty($this->_columns[$columnName])) {
            throw new LocalizedException(__('Wrong column name specified.'));
        }
        $column    = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        if ($column['renderer']) {
            $html = $column['renderer']->setInputName(
                $inputName
            )->setInputId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setColumnName(
                $columnName
            )->setColumn(
                $column
            )->toHtml();
        } else {

            $id    = $this->_getCellInputElementId('<%- _id %>', $columnName);
            $size  = $column['size'] ? 'size="' . $column['size'] . '"' : '';
            $class = isset($column['class']) ? $column['class'] : 'input-text';
            $style = isset($column['style']) ? ' style="' . $this->escapeHtmlAttr($column['style']) . '"' : '';

            $html = '<input type="text" id="' . $id . '"' . ' name="' . $inputName .
                '" value="<%- ' . $columnName . ' %>" ' . $size . ' class="' . $class . '"' . $style . '/>';
        }

        return $html;
    }

    /**
     * @return ImageFile
     * @throws LocalizedException
     */
    protected function getImageRenderer(): ImageFile
    {
        if (!$this->imageFileRenderer) {
            $this->imageFileRenderer = $this->getLayout()->createBlock(
                ImageFile::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->imageFileRenderer;
    }

    /**
     * Retrieve methods column renderer
     *
     * @return Methods
     * @throws LocalizedException
     */
    protected function getMethodsRenderer(): Methods
    {
        if (!$this->methodsRenderer) {
            $this->methodsRenderer = $this->getLayout()->createBlock(
                Methods::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->methodsRenderer->setClass('methods_select');
        }

        return $this->methodsRenderer;
    }

    /**
     * Returns base url for images
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getBaseImagePath(): string
    {
        $store        = $this->getStore();
        $baseMediaUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        $fullBaseUrl = $baseMediaUrl . Helper::IMAGE_URL_PATH;

        return $fullBaseUrl;
    }

    /**
     * @return Store
     * @throws NoSuchEntityException
     */
    public function getStore(): Store
    {
        if (!$this->store) {
            $this->store = $this->_storeManager->getStore();
        }

        return $this->store;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $optionExtraAttr = [];

        $optionExtraAttr['option_' . $this->getMethodsRenderer()->calcOptionHash($row->getData('methods_id'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
