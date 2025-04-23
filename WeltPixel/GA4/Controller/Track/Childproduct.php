<?php
namespace WeltPixel\GA4\Controller\Track;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use WeltPixel\GA4\Model\Dimension as DimensionModel;


class Childproduct extends Action
{
    /**
     * @var \WeltPixel\GA4\Helper\Data
     */
    protected $ga4Helper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DimensionModel
     */
    protected $dimensionModel;

    /**
     * @param Context $context
     * @param \WeltPixel\GA4\Helper\Data $ga4Helper
     * @param ProductRepositoryInterface $productRepository
     * @param DimensionModel $dimensionModel
     */
    public function __construct(
        Context $context,
        \WeltPixel\GA4\Helper\Data $ga4Helper,
        ProductRepositoryInterface $productRepository,
        DimensionModel $dimensionModel
    ) {
        parent::__construct($context);
        $this->ga4Helper = $ga4Helper;
        $this->productRepository = $productRepository;
        $this->dimensionModel = $dimensionModel;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $productId = $this->getRequest()->getPostValue('product_id');
        $result = '';

        if (!$productId) {
            return $this->prepareResult('');
        }

        $variant = $this->getRequest()->getPostValue('variant');

        if ($this->ga4Helper->isEnabled()) {
            try {
                $product = $this->productRepository->getById($productId);
            } catch (\Exception $ex) {
                return $this->prepareResult($result);
            }

            $currencyCode = $this->ga4Helper->getCurrencyCode();
            $productPrice = floatval(number_format($product->getPriceInfo()->getPrice('final_price')->getValue(), 2, '.', ''));

            $productItemOptions = [];
            $productItemOptions['item_name'] = html_entity_decode($product->getName() ?? '');
            $productItemOptions['item_id'] = $this->ga4Helper->getGtmProductId($product);
            $productItemOptions['affiliation'] = $this->ga4Helper->getAffiliationName();
            $productItemOptions['price'] = $productPrice;
            if ($this->ga4Helper->isBrandEnabled()) {
                $productItemOptions['item_brand'] = $this->ga4Helper->getGtmBrand($product);
            }

            $productCategoryIds = $product->getCategoryIds();
            $ga4Categories = $this->ga4Helper->getGA4CategoriesFromCategoryIds($productCategoryIds);
            $productItemOptions = array_merge($productItemOptions, $ga4Categories);
            $productItemOptions['quantity'] = 1;
            $productItemOptions['index'] = 0;
            $categoryName = $this->ga4Helper->getGtmCategoryFromCategoryIds($productCategoryIds);
            $productItemOptions['item_list_name'] = $categoryName;
            $productItemOptions['item_list_id'] = count($productCategoryIds) ? $productCategoryIds[0] : '';

            if ($this->ga4Helper->isVariantEnabled() && $variant) {
                $productItemOptions['item_variant'] = $variant;
            }

            /**  Set the custom dimensions */
            $customDimensions = $this->dimensionModel->getProductDimensions($product, $this->ga4Helper);
            foreach ($customDimensions as $name => $value) :
                $productItemOptions[$name] = $value;
            endforeach;

            $ecommerceData = [
                'value' => $productPrice,
                'currency' => $currencyCode,
                'items' => [$productItemOptions],
                'event' => 'view_item'
            ];

            $result = [
               'ecommerce' => $ecommerceData,
               'event' => 'view_item'
            ];
        }

        return $this->prepareResult($result);
    }

    /**
     * @param array $result
     * @return string
     */
    protected function prepareResult($result)
    {
        $jsonData = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
