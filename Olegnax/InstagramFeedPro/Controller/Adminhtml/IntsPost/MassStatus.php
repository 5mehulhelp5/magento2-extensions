<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory;

class MassStatus extends Action
{

    const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsPost_update';

    public $filter;

    public $collectionFactory;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function execute()
    {
        $status = (bool)$this->getRequest()->getParam('status');
        $collection = $this->filter->getCollection($this->collectionFactory->create())
            ->addFieldToFilter(
                'is_active',
                !$status
            );
        $items = 0;
        foreach ($collection as $item) {
            try {
                $item->setIsActive($status)->save();
                $items++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__(
                    'Something went wrong while updating status for %1.',
                    $item->getName()
                ));
            }
        }

        if ($items) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $items));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
