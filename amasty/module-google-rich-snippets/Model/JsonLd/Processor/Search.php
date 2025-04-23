<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Magento\Framework\UrlInterface;

class Search implements ProcessorInterface
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ConfigHelper $configHelper,
        UrlInterface $urlBuilder
    ) {
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function process(array $data): array
    {
        if (!$this->configHelper->forSearchEnabled()) {
            return $data;
        }
        $data = $this->addWebsiteData($data);
        $data['website']['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => $this->urlBuilder->getUrl('catalogsearch/result') . "?q={search_term_string}",
            'query-input' => 'required name=search_term_string'
        ];

        return $data;
    }

    private function addWebsiteData(array $data): array
    {
        if (isset($data['website'])) {
            return $data;
        }

        $data['website'] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => $this->urlBuilder->getBaseUrl()
        ];

        return $data;
    }
}
