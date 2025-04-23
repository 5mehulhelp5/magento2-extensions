<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd;

use Amasty\SeoRichData\Block\Product as ProductBlock;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Amasty\SeoRichData\Model\ConfigProvider;
use Amasty\SeoRichData\Model\JsonLd\Processor\ProductProcessorInterface;
use Amasty\SeoRichData\Model\Review\GetAggregateRating;
use Amasty\SeoRichData\Model\Review\GetReviews;
use Amasty\SeoRichData\Model\Source\Product\Description as DescriptionSource;
use Amasty\SeoRichData\Model\Source\Product\OfferItemCondition as OfferItemConditionSource;
use DateTimeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;
use Magento\Store\Model\StoreManagerInterface;

class ProductInfo
{
    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var ImageHelper
     */
    private $imageHelper;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var OfferItemConditionSource
     */
    private $offerItemConditionSource;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var GetReviews
     */
    private $getReviews;

    /**
     * @var GetAggregateRating
     */
    private $getAggregateRating;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var ProductProcessorInterface[]
     */
    private $processors;

    public function __construct(
        PageConfig $pageConfig,
        StoreManagerInterface $storeManager,
        StockRegistryInterface $stockRegistry,
        ConfigHelper $configHelper,
        ImageHelper $imageHelper,
        DateTime $dateTime,
        ConfigProvider $configProvider,
        OfferItemConditionSource $offerItemConditionSource,
        ProductResource $productResource,
        GetReviews $getReviews,
        GetAggregateRating $getAggregateRating,
        FilterManager $filterManager,
        array $processors = []
    ) {
        $this->pageConfig = $pageConfig;
        $this->storeManager = $storeManager;
        $this->stockRegistry = $stockRegistry;
        $this->configHelper = $configHelper;
        $this->imageHelper = $imageHelper;
        $this->dateTime = $dateTime;
        $this->configProvider = $configProvider;
        $this->offerItemConditionSource = $offerItemConditionSource;
        $this->productResource = $productResource;
        $this->getReviews = $getReviews;
        $this->getAggregateRating = $getAggregateRating;
        $this->filterManager = $filterManager;
        $this->processors = $processors;
    }

    public function extract(ProductModel $product = null): array
    {
        $productDescription = $this->replaceDescription($product);
        $offers = $this->prepareOffers($product);
        $offers = $this->unsetUnnecessaryData($offers);
        $image = $this->imageHelper->init(
            $product,
            'product_page_image_medium_no_frame',
            ['type' => 'image']
        )->getUrl();
        $resultArray = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->getName(),
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            'description' => $this->filterManager->stripTags(html_entity_decode($productDescription)),
            'image' => $image,
            'offers' => $offers,
            'url' => $product->getProductUrl()
        ];

        if ($this->configProvider->isShowRating()) {
            $resultArray['aggregateRating'] = $this->getAggregateRating->execute($product);
            $resultArray['review'] = $this->getReviews->execute((int)$product->getId());
        }

        if ($brandInfo = $this->getBrandInfo($product)) {
            $resultArray['brand'] = $brandInfo;
        }

        if ($manufacturerInfo = $this->getManufacturerInfo($product)) {
            $resultArray['manufacturer'] = $manufacturerInfo;
        }

        $this->updateCustomProperties($resultArray, $product);

        foreach ($this->processors as $processor) {
            $resultArray = $processor->process($resultArray, $product);
        }

        return $resultArray;
    }

    protected function prepareOffers(ProductModel $product): array
    {
        $offers = [];
        $priceCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $orgName = $this->storeManager->getStore()->getFrontendName();
        $productType = $product->getTypeId();

        switch ($productType) {
            case ConfigurableType::TYPE_CODE:
            case GroupedType::TYPE_CODE:
                if ($this->configHelper->showAggregate($productType)) {
                    $offers[] = $this->generateAggregateOffers(
                        $this->getSimpleProducts($product),
                        $priceCurrency
                    );
                } elseif ($this->configHelper->showAsList($productType)) {
                    foreach ($this->getSimpleProducts($product) as $child) {
                        $offers[] = $this->generateOffers($child, $priceCurrency, $orgName, $product);
                    }
                } else {
                    $offers[] = $this->generateOffers($product, $priceCurrency, $orgName);
                }
                break;
            default:
                $offers[] = $this->generateOffers($product, $priceCurrency, $orgName);
        }

        return $offers;
    }

    private function replaceDescription(ProductModel $product): string
    {
        return preg_replace(
            '#(\<style\>)(.*?)(\<\/style\>)#ims',
            '',
            $this->getProductDescription($product)
        );
    }

    private function getSimpleProducts(ProductModel $product): array
    {
        $list = [];
        $typeInstance = $product->getTypeInstance();

        switch ($product->getTypeId()) {
            case ConfigurableType::TYPE_CODE:
                $list = $typeInstance->getUsedProducts($product);
                break;
            case GroupedType::TYPE_CODE:
                $list = $typeInstance->getAssociatedProducts($product);
                break;
        }

        return $list;
    }

    private function generateAggregateOffers(array $listOfSimples, string $priceCurrency): array
    {
        $minPrice = INF;
        $maxPrice = 0;
        $offerCount = 0;

        foreach ($listOfSimples as $child) {
            $childPrice = $child->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
            $minPrice = min($minPrice, $childPrice);
            $maxPrice = max($maxPrice, $childPrice);
            $offerCount++;
        }

        return [
            '@type' => 'AggregateOffer',
            'lowPrice' => $minPrice ? round($minPrice, 2) : 0.0,
            'highPrice' => $maxPrice ? round($maxPrice, 2) : 0.0,
            'offerCount' => $offerCount,
            'priceCurrency' => $priceCurrency
        ];
    }

    protected function unsetUnnecessaryData(array $offers): array
    {
        if (!$this->configProvider->isShowAvailability()) {
            foreach ($offers as $key => $offer) {
                if (isset($offer['availability'])) {
                    unset($offers[$key]['availability']);
                }
            }
        }

        if (!$this->configHelper->showCondition()) {
            foreach ($offers as $key => $offer) {
                if (isset($offer['itemCondition'])) {
                    unset($offers[$key]['itemCondition']);
                }
            }
        }

        return $offers;
    }

    protected function generateOffers(
        ProductModel $product,
        string $priceCurrency,
        string $orgName,
        ?ProductModel $parentProduct = null
    ): array {
        if ($parentProduct
            && !in_array($this->getProductVisibility($product), ProductBlock::VISIBILITY)
        ) {
            $productUrl = $parentProduct->getProductUrl();
        } else {
            $productUrl = $product->getProductUrl();
        }
        $itemConditionValue = $product->hasData(OfferItemConditionSource::ATTRIBUTE_CODE)
            ? (int)$product->getData(OfferItemConditionSource::ATTRIBUTE_CODE)
            : OfferItemConditionSource::NEW_CONDITION;
        $offers = [
            '@type' => 'Offer',
            'priceCurrency' => $priceCurrency,
            'price' => $this->getPrice($product),
            'availability' => $this->getAvailabilityCondition($product),
            'itemCondition' => $this->offerItemConditionSource->getConditionValue($itemConditionValue),
            'seller' => [
                '@type' => 'Organization',
                'name' => $orgName
            ],
            'url' => $productUrl
        ];

        $this->updateCustomProperties($offers, $product);

        if ($this->configProvider->isReplacePriceValidUntil()
            && $product->getSpecialPrice()
            && $this->dateTime->timestamp() < $this->dateTime->timestamp($product->getSpecialToDate())
        ) {
            $offers['priceValidUntil'] = $this->dateTime->date(DateTimeInterface::ATOM, $product->getSpecialToDate());
        } elseif ($this->configProvider->getDefaultPriceValidUntil()) {
            $offers['priceValidUntil'] = $this->dateTime->date(
                DateTimeInterface::ATOM,
                $this->configProvider->getDefaultPriceValidUntil()
            );
        }

        return $offers;
    }

    private function getProductVisibility(ProductModel $product): int
    {
        $visibility = $product->getVisibility();
        if ($visibility === null) {
            $visibility = $this->productResource->getAttributeRawValue(
                $product->getId(),
                ProductInterface::VISIBILITY,
                $this->storeManager->getStore()->getId()
            );
        }

        return (int)$visibility;
    }

    private function getBrandInfo(ProductModel $product): ?array
    {
        $brand = $this->configHelper->getBrandAttribute();
        if (!$brand) {
            return null;
        }

        $attributeValue = $product->getAttributeText($brand) ?: $product->getData($brand);
        if ($attributeValue) {
            $info = [
                '@type' => 'Brand',
                'name' => $attributeValue
            ];
        }

        return $info ?? null;
    }

    private function getManufacturerInfo(ProductModel $product): ?array
    {
        $info = null;
        $manufacturer = $this->configHelper->getManufacturerAttribute();

        if ($manufacturer && $attributeValue = $product->getAttributeText($manufacturer)) {
            $info = [
                '@type' => 'Organization',
                'name' => $attributeValue
            ];
        }

        return $info;
    }

    public function getAvailabilityCondition(ProductModel $product): string
    {
        $availabilityCondition = $this->stockRegistry->getProductStockStatus($product->getId())
            ? ProductBlock::IN_STOCK
            : ProductBlock::OUT_OF_STOCK;

        return $availabilityCondition;
    }

    private function updateCustomProperties(array &$result, ProductModel $product): void
    {
        foreach ($this->configHelper->getCustomAttributes() as $pair) {
            $snippetProperty = isset($pair[0]) ? trim($pair[0]) : null;
            $attributeCode = isset($pair[1]) ? trim($pair[1]) : $snippetProperty;

            if ($snippetProperty && $attributeCode) {
                if ($product->getData($attributeCode)) {
                    $result[$snippetProperty] = $product->getAttributeText($attributeCode)
                        ? $product->getAttributeText($attributeCode)
                        : $product->getData($attributeCode);
                }
            }
        }
    }

    private function getProductDescription(ProductModel $product): string
    {
        $description = '';
        switch ($this->configProvider->getProductDescriptionMode((int)$this->storeManager->getStore()->getId())) {
            case DescriptionSource::SHORT_DESCRIPTION:
                $description = $this->getMetaData($product, 'short_description') ?: $product->getShortDescription();
                break;
            case DescriptionSource::FULL_DESCRIPTION:
                $description = $this->getMetaData($product, 'description') ?: $product->getDescription();
                break;
            case DescriptionSource::META_DESCRIPTION:
                $description = $this->getMetaData($product, 'meta_description')
                    ?: $this->pageConfig->getDescription();
                break;
        }

        return (string)$description;
    }

    /**
     * Value of this method resolved in Amasty_Meta \Amasty\Meta\Plugin\SeoRichData\Block\Product
     */
    public function getMetaData(ProductModel $product, string $key): string
    {
        return '';
    }

    private function getPrice(ProductModel $product): float
    {
        $msrp = (float)$product->getMsrp();
        $price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue() ?? 0;

        return round(max($msrp, $price), 2);
    }
}
