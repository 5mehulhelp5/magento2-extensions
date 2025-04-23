<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Block;

use Amasty\SeoRichData\Helper\Category as CategoryHelper;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Amasty\SeoRichData\Model\ConfigProvider;
use Amasty\SeoRichData\Model\CountryInfo;
use Amasty\SeoRichData\Model\DataCollector;
use Amasty\SeoRichData\Model\JsonLd\ProcessorProvider;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Store\Model\StoreManagerInterface;

class JsonLd extends AbstractBlock
{
    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var CategoryHelper
     */
    protected $categoryHelper = null;

    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ProcessorProvider
     */
    private $processorProvider;

    /**
     * @var CountryInfo
     */
    private $countryInfo;

    public function __construct(
        Context $context,
        DataCollector $dataCollector,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CategoryHelper $categoryHelper,
        EncoderInterface $jsonEncoder,
        PageConfig $pageConfig,
        ConfigHelper $configHelper,
        LayerResolver $layerResolver,
        ConfigProvider $configProvider,
        array $data = [],
        ProcessorProvider $processorProvider = null, // TODO move to not optional
        CountryInfo $countryInfo = null // TODO move to not optional
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->jsonEncoder = $jsonEncoder;
        $this->dataCollector = $dataCollector;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->pageConfig = $pageConfig;
        $this->configHelper = $configHelper;
        $this->layerResolver = $layerResolver;
        $this->configProvider = $configProvider;
        // OM for backward compatibility
        $this->processorProvider = $processorProvider ?? ObjectManager::getInstance()->get(ProcessorProvider::class);
        $this->countryInfo = $countryInfo ?? ObjectManager::getInstance()->get(CountryInfo::class);
    }

    protected function _toHtml(): string
    {
        $data = [];
        foreach ($this->processorProvider->getProcessors() as $processor) {
            $data = $processor->process($data);
        }

        $result = '';
        foreach ($data as $section) {
            $result .= sprintf(
                '<script type="application/ld+json">%s</script>',
                $this->jsonEncoder->encode($section)
            );
        }

        return $result;
    }

    /**
     * @deprecated prepare data logic is moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\ProcessorInterface::process()
     */
    protected function prepareData(): array
    {
        $data = [];

        $this->addBreadcrumbsData($data);
        $this->addWebsiteName($data);
        $this->addOrganizationData($data);
        $this->addCategoryData($data);
        $this->addSearchData($data);
        $this->addSocialProfiles($data);

        return $data;
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\Website::process()
     */
    protected function addWebsiteName(&$data)
    {
        if (!$this->configHelper->forWebsiteEnabled()) {
            return;
        }

        $name = $this->configHelper->getWebsiteName();

        if ($name) {
            $this->addWebsiteData($data);
            $data['website']['name'] = $name;
        }
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\Breadcrumbs::process()
     */
    protected function addBreadcrumbsData(&$data)
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

                $items []= [
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
                    '@context'        => 'https://schema.org',
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => $items
                ];
            }
        }
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\Organization::process()
     */
    protected function addOrganizationData(&$data)
    {
        if (!$this->configHelper->forOrganizationEnabled()) {
            return;
        }

        $data['organization'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];

        if ($name = $this->configHelper->getOrganizationName()) {
            $data['organization']['name'] = $name;
        }

        if ($logoUrl = $this->configHelper->getOrganizationLogo()) {
            $data['organization']['logo'] = $logoUrl;
        }

        if ($description = $this->configHelper->getOrganizationDescription()) {
            $data['organization']['description'] = $description;
        }

        foreach ($this->configHelper->getOrganizationContacts() as $contactType => $contact) {
            $data['organization']['contactPoint'][] = [
                "@type" => "ContactPoint",
                "telephone" => $contact,
                "contactType" => str_replace('_', ' ', $contactType)
            ];
        }

        if ($country = $this->countryInfo->getCountryId()) {
            $data['organization']['address']['addressCountry'] = $country;
        }

        if ($postalCode = $this->configHelper->getPostalCode()) {
            $data['organization']['address']['postalCode'] = $postalCode;
        }

        if ($region = $this->configHelper->getOrganizationRegion()) {
            $data['organization']['address']['addressRegion'] = $region;
        }

        if ($city = $this->configHelper->getOrganizationCity()) {
            $data['organization']['address']['addressLocality'] = $city;
        }

        if ($city = $this->configProvider->getStreetAddress()) {
            $data['organization']['address']['streetAddress'] = $city;
        }
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\Category::process()
     */
    protected function addCategoryData(&$data)
    {
        if (!$this->configHelper->forCategoryEnabled()) {
            return;
        }

        $category = $this->getCurrentCategory();
        if (!$category) {
            return;
        }

        if ('category' != $this->_request->getControllerName()) {
            return;
        }

        $data['category'] = $this->generateProductsInfo();
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\Search::process()
     */
    protected function addSearchData(&$data)
    {
        if (!$this->configHelper->forSearchEnabled()) {
            return;
        }
        $this->addWebsiteData($data);
        $data['website']['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => $this->_urlBuilder->getUrl('catalogsearch/result') . "?q={search_term_string}",
            'query-input' => 'required name=search_term_string'
        ];
    }

    protected function addWebsiteData(&$data)
    {
        if (isset($data['website'])) {
            return;
        }

        $data['website'] = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];
    }

    /**
     * @deprecated moved to processor
     * @see \Amasty\SeoRichData\Model\JsonLd\Processor\SocialProfiles::process()
     * Add person information
     *
     * @param $data
     */
    private function addSocialProfiles(&$data)
    {
        if ($this->configHelper->forSocialEnabled()
            && $this->configHelper->forOrganizationEnabled()
        ) {
            foreach ($this->configHelper->getSocialLinks() as $socialLink) {
                $data['organization']['sameAs'][] = $socialLink;
            }
        }
    }

    /**
     * @return array
     */
    private function generateProductsInfo()
    {
        $productCollection = $this->layerResolver->get()->getProductCollection();
        $productsInfo = [];
        /** @var \Amasty\SeoRichData\Block\Product $productBlock */
        $productBlock = $this->getLayout()->createBlock(
            \Amasty\SeoRichData\Block\Product::class
        );
        foreach ($productCollection as $product) {
            $productBlock->setProduct($product);
            $productInfo = $productBlock->getResultArray();
            $productsInfo[] = $productInfo;
        }

        return $productsInfo;
    }

    private function getCurrentCategory(): ?Category
    {
        return $this->coreRegistry->registry('current_category');
    }

    private function getCurrentProduct(): ?ProductModel
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
