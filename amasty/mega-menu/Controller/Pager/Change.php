<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Controller\Pager;

use Amasty\MegaMenu\Block\Product\ProductsSlider;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutFactory;

class Change extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    public function __construct(
        JsonFactory $resultJsonFactory,
        LayoutFactory $layoutFactory,
        Context $context
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax()) {
            $widgetData = $this->getRequest()->getParam('widget_data');
            $resultJson = $this->resultJsonFactory->create();

            if ($widgetData) {
                $block = $this->getBlock($widgetData);
                $html = $block->toHtml();
            }
            $result['block'] = $html ?? '';

            return $resultJson->setData($result);
        } else {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }

    public function getBlock(array $widgetData): BlockInterface
    {
        $layout = $this->layoutFactory->create();

        return $layout->createBlock(
            ProductsSlider::class,
            $widgetData['name'],
            ['data' => $widgetData['data']]
        );
    }
}
