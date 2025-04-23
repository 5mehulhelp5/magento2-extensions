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

class MassDelete extends Action
{

    const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsPost_delete';

    protected $_filter;
    protected $_collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_collectionFactory->create();
        $collectionFilter = $this->_filter->getCollection($collection);
        $delete = 0;
        if (!empty($collectionFilter)) {
            foreach ($collection as $item) {
                try {
                    $item->delete();
                    $delete++;
                } catch (Exception $e) {
                    // display error message
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
            if (0 < $delete) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $delete));
            }

        } else {
            $this->messageManager->addErrorMessage(__('Nothing selected!'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
