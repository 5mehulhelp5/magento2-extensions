<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Link;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;

class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var \Amasty\MegaMenuLite\Model\Repository\LinkRepository
     */
    private $linkRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        \Amasty\MegaMenuLite\Model\Repository\LinkRepository $linkRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->linkRepository = $linkRepository;
        $this->logger = $logger;
    }

    public function execute(): Redirect
    {
        $packId = (int)$this->getRequest()->getParam('id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($packId) {
            try {
                $this->linkRepository->deleteById($packId);
                $this->messageManager->addSuccessMessage(__('The link have been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/edit', ['id' => $packId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
