<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Amasty\SeoRichData\Model\DataCollector;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;

class Breadcrumbs implements ProcessorInterface
{
    /**
     * @var DataCollector
     */
    private $dataCollector;

    /**
     * @var Registry
     */
    private $coreRegistry = null;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    public function __construct(
        DataCollector $dataCollector,
        Registry $coreRegistry,
        ConfigHelper $configHelper
    ) {
        $this->dataCollector = $dataCollector;
        $this->coreRegistry = $coreRegistry;
        $this->configHelper = $configHelper;
    }

    public function process(array $data): array
    {
        $breadcrumbs = $this->dataCollector->getData('breadcrumbs');
        if (is_array($breadcrumbs)) {
            $items = [];
            $position = 0;
            foreach ($breadcrumbs as $key => $breadcrumb) {
                $link = $this->resolveBreadcrumbLink($key, $breadcrumb);

                if (!$link) {
                    continue;
                }

                $items[] = [
                    '@type' => 'ListItem',
                    'position' => ++$position,
                    'item' => [
                        '@id' => $link,
                        'name' => $breadcrumb['label']
                    ]
                ];
            }

            if (count($items) > 0) {
                if ($this->configHelper->sliceBreadcrumbs()) {
                    $items = array_slice($items, -1, 1);
                    if (isset($items[0])) {
                        $items[0]['position'] = 1;
                    }
                }

                $data['breadcrumbs'] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => $items
                ];
            }
        }

        return $data;
    }

    private function getCurrentCategory(): ?CategoryInterface
    {
        return $this->coreRegistry->registry('current_category');
    }

    private function getCurrentProduct(): ?ProductInterface
    {
        return $this->coreRegistry->registry('current_product');
    }

    private function resolveBreadcrumbLink(string $key, array $breadcrumb): ?string
    {
        $link = $breadcrumb['link'];

        if (!$link && $this->getCurrentCategory() && !$this->getCurrentProduct()) {
            $link = $this->getCurrentCategory()->getUrl();
        }

        if (!$link && $key === 'product') {
            $link = $this->getProductLink();
        }

        return $link;
    }

    private function getProductLink(): ?string
    {
        if ($product = $this->getCurrentProduct()) {
            $link = $product->getProductUrl();
        }

        return $link ?? null;
    }
}
