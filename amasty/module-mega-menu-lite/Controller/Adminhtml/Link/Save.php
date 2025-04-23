<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Link;

use Amasty\MegaMenuLite\Model\Backend\SaveLink\SaveProcessor;
use Amasty\MegaMenuLite\Model\Menu\Link;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var SaveProcessor
     */
    private $saveProcessor;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger,
        SaveProcessor $saveProcessor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        $this->saveProcessor = $saveProcessor;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $entityId = (int) $this->getRequest()->getParam(Link::ENTITY_ID);
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                $linkEntityId = $this->saveProcessor->execute($data);
                $this->messageManager->addSuccessMessage(__('The Custom Menu Item was successfully saved.'));
                $this->dataPersistor->clear(Link::PERSIST_NAME);

                if ($this->getRequest()->getParam('back')) {
                    $store = (int) $this->_request->getParam('store_id', Store::DEFAULT_STORE_ID);

                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $linkEntityId,
                            'store' => $store
                        ]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($entityId) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
                } else {
                    return $resultRedirect->setPath('*/*/newAction');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the link data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->dataPersistor->set(Link::PERSIST_NAME, $data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
