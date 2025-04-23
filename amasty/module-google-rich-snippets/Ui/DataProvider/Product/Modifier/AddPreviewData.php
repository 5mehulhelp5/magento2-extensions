<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Ui\DataProvider\Product\Modifier;

use Amasty\SeoRichData\Model\Backend\Preview\GetRichData;
use Amasty\SeoRichData\Model\ConfigProvider;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class AddPreviewData implements ModifierInterface
{
    private const PREVIEW_DATA_SCOPE = '%d/product/rich_data_preview';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var GetRichData
     */
    private $getRichData;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        LocatorInterface $locator,
        GetRichData $getRichData,
        ArrayManager $arrayManager,
        ConfigProvider $configProvider
    ) {
        $this->locator = $locator;
        $this->getRichData = $getRichData;
        $this->arrayManager = $arrayManager;
        $this->configProvider = $configProvider;
    }

    public function modifyData(array $data): array
    {
        $storeId = (int) $this->locator->getStore()->getId();
        if ($this->configProvider->isEnabledForProduct($storeId) && $this->locator->getProduct()->getId()) {
            $previewRichData = $this->getRichData->execute($this->locator->getProduct(), $storeId);
            $data = $this->arrayManager->set(
                sprintf(self::PREVIEW_DATA_SCOPE, $this->locator->getProduct()->getId()),
                $data,
                $previewRichData
            );
        }

        return $data;
    }

    public function modifyMeta(array $meta): array
    {
        if ($this->configProvider->isEnabledForProduct((int) $this->locator->getStore()->getId())) {
            if (!isset($meta['search-engine-optimization'])) {
                $meta = array_replace_recursive($meta, $this->generateSeoElement());
            }

            $meta = array_replace_recursive($meta, $this->generateRichDataPreviewElement());
        }

        return $meta;
    }

    protected function generateSeoElement(): array
    {
        $result = [
            'search-engine-optimization' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Fieldset::NAME,
                            'collapsible' => true,
                            'dataScope' => 'data.product',
                            'label' => __('Search Engine Optimization'),
                            'sortOrder' => 40
                        ],
                    ],
                ],
                'children' => [],
            ],
        ];

        return $result;
    }

    protected function generateRichDataPreviewElement(): array
    {
        $result = [
            'search-engine-optimization' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'admin__fieldset-product-websites'
                        ],
                    ],
                ],
                'children' => [
                    'rich_data_preview' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => Field::NAME,
                                    'formElement' => Textarea::NAME,
                                    'dataType' => Text::NAME,
                                    'component' => 'Amasty_SeoRichData/js/components/rich-data-preview',
                                    'label' => __('Rich Data Preview'),
                                    'notice' => __('Kindly note that values displayed in the preview snippet
                                    might be different on the frontend'),
                                    'additionalClasses' => 'amseorichdata-component-wrapper',
                                    'sortOrder' => 0
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $result;
    }
}
