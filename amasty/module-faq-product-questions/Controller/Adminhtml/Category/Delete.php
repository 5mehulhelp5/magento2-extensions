<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Controller\Adminhtml\Category;

use Amasty\Faq\Api\CategoryRepositoryInterface;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;

class Delete extends \Amasty\Faq\Controller\Adminhtml\Category
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $repository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        CategoryRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the category.'));

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);

                return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to delete.'));

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
