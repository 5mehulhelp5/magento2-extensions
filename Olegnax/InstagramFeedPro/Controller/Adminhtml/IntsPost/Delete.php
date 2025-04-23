<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Delete extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsPost_delete';

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('intspost_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(IntsPost::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Post.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['intspost_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Post to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
