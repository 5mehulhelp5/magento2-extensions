<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Builder;

use Amasty\MegaMenuLite\Model\Backend\Builder\Registry as BuilderRegistry;
use Amasty\MegaMenuLite\Model\Backend\Builder\UpdatePositionData;
use Amasty\MegaMenuLite\Model\ResourceModel\Menu\Item\Position;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_builder';

    /**
     * @var Position
     */
    private $positionResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var BuilderRegistry
     */
    private $registry;

    /**
     * @var UpdatePositionData
     */
    private $updatePositionData;

    public function __construct(
        Action\Context $context,
        UpdatePositionData $updatePositionData,
        StoreManagerInterface $storeManager,
        PageFactory $resultPageFactory,
        BuilderRegistry $registry
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->updatePositionData = $updatePositionData;
    }

    public function execute(): Page
    {
        $storeId = (int) $this->getRequest()->getParam('store', $this->storeManager->getDefaultStoreView()->getId());
        $this->registry->setStoreId($storeId);
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->updatePositionData->execute($storeId);

        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('Menu Builder'));

        return $resultPage;
    }
}
