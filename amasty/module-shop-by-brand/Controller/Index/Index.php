<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Brand for Magento 2
 */

namespace Amasty\ShopbyBrand\Controller\Index;

use Magento\Theme\Block\Html\Breadcrumbs;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Magento\Catalog\Model\Design
     */
    protected $catalogDesign;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $categoryUrlPathGenerator;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var  \Amasty\ShopbyBase\Model\Category\Manager
     */
    protected $categoryManager;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $brandHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\ShopbyBase\Model\Category\Manager $categoryManager,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->catalogDesign = $catalogDesign;
        $this->catalogSession = $catalogSession;
        $this->coreRegistry = $coreRegistry;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->layerResolver = $layerResolver;
        $this->categoryRepository = $categoryRepository;
        $this->categoryManager = $categoryManager;
        $this->brandHelper = $brandHelper;
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\CategoryInterface
     */
    protected function _initCategory()
    {
        return $this->categoryManager->init($this);
    }

    /**
     * @return $this|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->initBrand();
        $category = $this->_initCategory();
        if (!$category) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $settings = $this->catalogDesign->getDesignSettings($category);

        // apply custom design
        if ($settings->getCustomDesign()) {
            $this->catalogDesign->applyCustomDesign($settings->getCustomDesign());
        }

        $this->catalogSession->setLastViewedCategoryId($category->getId());

        $page = $this->resultPageFactory->create();
        // apply custom layout (page) template once the blocks are generated
        if ($settings->getPageLayout()) {
            $page->getConfig()->setPageLayout($settings->getPageLayout());
        }

        $type = 'layered';
        if (!$category->hasChildren()) {
            // Two levels removed from parent.  Need to add default page type.
            $page->addPageLayoutHandles(['type' => $type]);
            $type = 'layered_without_children';
        }

        $page->addPageLayoutHandles(['type' => $type, 'id' => $category->getId()], 'catalog_category_view');

        // apply custom layout update once layout is loaded
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates && is_array($layoutUpdates)) {
            foreach ($layoutUpdates as $layoutUpdate) {
                $page->addUpdate($layoutUpdate);
            }
        }

        $page->getConfig()->addBodyClass('page-products')
            ->addBodyClass('categorypath-' . $this->categoryUrlPathGenerator->getUrlPath($category))
            ->addBodyClass('category-' . $category->getUrlKey());

        if ($category->getMetaTitle()) {
            $page->getConfig()->getTitle()->set($category->getMetaTitle());
        } else {
            $page->getConfig()->getTitle()->set($category->getName());
        }

        /** @var Breadcrumbs $breadcrumbsBlock */
        if ($breadcrumbsBlock = $page->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'all-products',
                [
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                ]
            );
        }

        return $page;
    }

    /**
     * @return $this
     */
    private function initBrand()
    {
        if ($id = $this->getRequest()->getParam('id', false)) {
            $this->getRequest()->setParams([$this->brandHelper->getBrandAttributeCode() => $id]);
        }
        return $this;
    }
}
