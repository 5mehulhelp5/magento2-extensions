<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Plugin\Review\Block\Adminhtml;

class GridPlugin
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Magento\Review\Block\Adminhtml\Grid $subject
     *
     * @return array
     */
    public function beforeGetMassactionBlockHtml(\Magento\Review\Block\Adminhtml\Grid $subject)
    {
        $massBlock = $subject->getMassactionBlock();
        if ($massBlock) {
            $massBlock->addItem(
                'all_approve',
                [
                    'label' => __('Approve for All Store Views'),
                    'url' => $this->getUrl(
                        'amasty_advancedreview/review/massAllApprove',
                        ['ret' => $this->registry->registry('usePendingFilter') ? 'pending' : 'index']
                    ),
                    'confirm' => __('Are you sure?')
                ]
            );

            $massBlock->addItem(
                'all_approve_website',
                [
                    'label' => __('Approve for All Store Views within the Website'),
                    'url' => $this->getUrl(
                        'amasty_advancedreview/review/massAllApproveWebsite',
                        ['ret' => $this->registry->registry('usePendingFilter') ? 'pending' : 'index']
                    ),
                    'confirm' => __('Are you sure?')
                ]
            );

            $massBlock->addItem(
                'all_approve_product',
                [
                    'label' => __('Approve for All Store Views Where Product is Assigned'),
                    'url' => $this->getUrl(
                        'amasty_advancedreview/review/massAllApproveProduct',
                        ['ret' => $this->registry->registry('usePendingFilter') ? 'pending' : 'index']
                    ),
                    'confirm' => __('Are you sure?')
                ]
            );
        }
        return [];
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
