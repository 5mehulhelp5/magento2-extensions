<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsUser;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Olegnax\InstagramFeedPro\Model\IntsUser;

class Delete extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsUser
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsUser_delete';

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
        $id = $this->getRequest()->getParam('intsuser_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(IntsUser::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('User deleted successfully!'));
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go to grid
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            // display error message
            $this->messageManager->addErrorMessage(__('Unable to find User to delete.'));
        }
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
