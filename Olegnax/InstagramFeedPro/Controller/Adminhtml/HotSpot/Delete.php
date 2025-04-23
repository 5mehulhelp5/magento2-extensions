<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\HotSpot;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Olegnax\InstagramFeedPro\Model\HotSpot;

class Delete extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\HotSpot
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::HotSpot_delete';
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
        $id = $this->getRequest()->getParam('hotspot_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(HotSpot::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Hotspot.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['hotspot_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Hotspot to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

