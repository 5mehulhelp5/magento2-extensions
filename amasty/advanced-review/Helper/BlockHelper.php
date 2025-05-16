<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Helper;

use Amasty\AdvancedReview\Block\Helpful;
use Amasty\AdvancedReview\Block\Images;
use Amasty\AdvancedReview\Block\Review\Toolbar;
use Amasty\AdvancedReview\Model\Sources\Recommend;
use Amasty\AdvancedReview\Model\Sources\WhoCanSubmit;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class BlockHelper implements \Magento\Framework\Data\CollectionDataSourceInterface
{

    public const ADMIN_ANSWER_ACCOUNT_ONLY = 'amasty_admin_answer_account_only';

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    private $stringUtils;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\AdvancedReview\Model\Sources\Sort
     */
    private $sortModel;

    /**
     * @var \Amasty\AdvancedReview\ViewModel\Summary\SummaryRendererInterface
     */
    private $summaryRenderer;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Amasty\AdvancedReview\Helper\Config $config,
        \Magento\Framework\Stdlib\StringUtils $stringUtils,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\AdvancedReview\Model\Sources\Sort $sortModel,
        \Amasty\AdvancedReview\ViewModel\Summary\SummaryRendererInterface $summaryRenderer,
        DataObjectFactory $dataObjectFactory,
        ManagerInterface $eventManager
    ) {
        $this->blockFactory = $blockFactory;
        $this->config = $config;
        $this->stringUtils = $stringUtils;
        $this->sessionFactory = $sessionFactory;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->sortModel = $sortModel;
        $this->summaryRenderer = $summaryRenderer;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @return mixed
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return string
     */
    public function getReviewAnswerHtml($review)
    {
        if ($this->config->isAllowAnswer()
            && $review->getAnswer()
            && !$this->config->isAdminAnswerAvailableOnAccountOnly($review)
        ) {
            $html = $review->getAnswer();
        }

        return $html ?? '';
    }

    /**
     * @return bool
     */
    public function isProsConsEnabled()
    {
        return $this->config->isProsConsEnabled();
    }

    /**
     * @param $review
     * @return string
     */
    public function getVerifiedBuyerHtml($review)
    {
        $html = '';
        if ($review->getVerifiedBuyer()) {
            $html .= sprintf(
                '<div class="amreview-verified">%s</div>',
                __('Verified Buyer')
            );
        }

        return $html;
    }

    /**
     * @param $review
     * @return string
     */
    public function getRecommendedHtml($review)
    {
        $html = '';
        if ($this->config->isRecommendFieldEnabled() && $review->getData('is_recommended') == Recommend::RECOMMENDED) {
            $html .= sprintf(
                '<p class="amreview-recommended">%s</p>',
                __('I recommend this product')
            );
        }

        return $html;
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @param bool $short
     * @return string
     */
    public function getHelpfulHtml($review, $short = false)
    {
        $html = '';
        if ($this->config->isAllowHelpful()) {
            /** @var Helpful $block */
            $block = $this->blockFactory->createBlock(Helpful::class)->setReview($review);
            if ($block) {
                if ($short) {
                    $block->useShortTemplate();
                }
                $html .= $block->toHtml();
            }
        }

        return $html;
    }

    /**
     * @param Product $product
     * @param ReviewCollection $displayedCollection
     *
     * @return string
     */
    public function getReviewsSummaryHtml($product, $displayedCollection)
    {
        return $this->summaryRenderer->render($displayedCollection, $product);
    }

    /**
     * @param int $reviewId
     * @param bool $addSizeStyles
     * @return string
     */
    public function getReviewImagesHtml($reviewId, $addSizeStyles = true)
    {
        $html = '';
        if ($this->config->isAllowImages()) {
            $block = $this->blockFactory
                ->createBlock(Images::class)
                ->setReviewId($reviewId)
                ->setAddSizeStyles($addSizeStyles)
                ->setSlidesToShow($this->getSlidesToShow());
            if ($block) {
                $html = $block->toHtml();
            }
        }
        return $this->replaceWithWebp($html);
    }

    /**
     * @return bool
     */
    public function isAllowGuest()
    {
        return $this->config->isAllowGuest() || $this->getCustomerSession()->getId();
    }

    /**
     * @return int
     */
    public function getSlidesToShow()
    {
        return (int)$this->config->getSlidesToShow();
    }

    /**
     * Get URL for ajax call
     *
     * @param array $params
     *
     * @return string
     */
    public function getProductReviewUrl($params = [])
    {
        $params['_secure'] = $this->request->isSecure();

        return $this->urlBuilder->getUrl(
            'review/product/listAjax',
            $params
        );
    }

    /**
     * used when reviews filtered by rating
     *
     * @param $filterStars
     * @param $productId
     *
     * @return string
     */
    public function getAdditionalTitle($filterStars, $productId)
    {
        $additionalTitle = '';
        if ($filterStars) {
            $additionalTitle = ' ' . __('with %1 stars | ', $filterStars);
            $additionalTitle .= ' <a rel="nofollow" title="' . __('Show All') . '" href="'
                . $this->getProductReviewUrl(['id' => $productId])
                . '">' . __('Show All') . '</a>';
        }

        return $additionalTitle;
    }

    /**
     * @param ReviewCollection $collection
     * @return string
     */
    public function getReviewToolbarHtml($collection)
    {
        $html = '';
        $block = $this->blockFactory->createBlock(Toolbar::class)
            ->setCollection($collection);
        if ($block) {
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function isCommentsAllowed()
    {
        return $this->config->isCommentsEnabled() && $this->canUserComment();
    }

    /**
     * @return bool
     */
    public function canUserComment()
    {
        return $this->config->isGuestCanComment() || $this->getCustomerSession()->getId();
    }

    public function replaceWithWebp(string $html): string
    {
        $data = $this->dataObjectFactory->create();
        $data->setData(
            [
                'page' => $html,
                'pageType' => 'catalog_product_view',
            ]
        );
        $this->eventManager->dispatch('amoptimizer_process_ajax_page', ['data' => $data]);

        return $data->getData('page');
    }
}
