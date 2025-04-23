<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Builder;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Move extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\MegaMenuLite\Model\Menu\Item\PositionFactory
     */
    private $positionFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\MegaMenuLite\Model\Menu\Item\PositionFactory $positionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->logger = $logger;
        $this->positionFactory = $positionFactory;
    }

    /**
     * Move category action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /**
         * Category id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->layoutFactory->create()->getMessagesBlock();
        $error = false;

        try {
            $positionItem = $this->initPositionItem();
            if ($positionItem === false) {
                throw new LocalizedException(__('Item is not available for requested store.'));
            }
            $positionItem->move($prevNodeId);
        } catch (LocalizedException $e) {
            $error = true;
            $this->messageManager->addExceptionMessage($e);
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addErrorMessage(__('There was a item move error.'));
            $this->logger->critical($e);
        }

        if (!$error) {
            $this->messageManager->addSuccessMessage(__('You moved the item.'));
        }

        $block->setMessages($this->messageManager->getMessages(true));
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'messages' => $block->getGroupedHtml(),
            'error' => $error
        ]);
    }

    /**
     * @return \Amasty\MegaMenuLite\Model\Menu\Item\Position
     */
    private function initPositionItem()
    {
        /** @var \Amasty\MegaMenuLite\Model\Menu\Item\Position $positionItem */
        $positionItem = $this->positionFactory->create();
        $positionItem->load($this->getRequest()->getParam('id'));

        return $positionItem;
    }
}
