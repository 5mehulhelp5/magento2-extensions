<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */
namespace Amasty\Shopby\Plugin\Catalog\Model\Category;

use Magento\Catalog\Model\Category\DataProvider as CategoryDataProvider;

class DataProvider
{
    /**
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPrepareMeta(
        CategoryDataProvider $subject,
        $result
    ) {
        $result['content']['children']['thumbnail']['arguments']['data']['config'] = [
            'dataType' => 'image',
            'formElement' => 'fileUploader',
            'visible' => true,
            'required' => false,
            'label' => __('Thumbnail'),
            'sortOrder' => 0,
            'notice' => null,
            'default' => null,
            'size' => null,
            'scopeLabel' => '[STORE VIEW]',
            'componentType' => 'field',
            'source' => 'category',
            'elementTmpl' => 'ui/form/element/uploader/uploader',
            'previewTmpl' => 'Magento_Catalog/image-preview',
            'uploaderConfig' => [
                'url' => 'amshopby_category_image/category_image/upload',
            ],
        ];

        return $result;
    }
}
