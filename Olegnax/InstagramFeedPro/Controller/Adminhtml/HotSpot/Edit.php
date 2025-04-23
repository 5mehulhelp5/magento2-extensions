<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\HotSpot;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Olegnax\InstagramFeedPro\Model\HotSpot;

class Edit extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\HotSpot
{
    const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::HotSpot_view';

    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('hotspot_id');
        $model = $this->_objectManager->create(HotSpot::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Hotspot no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('olegnax_instagramfeedpro_hotspot', $model);

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Hotspot') : __('New Hotspot'),
            $id ? __('Edit Hotspot') : __('New Hotspot')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Hotspots'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Hotspot %1',
            $model->getId()) : __('New Hotspot'));
        return $resultPage;
    }
}

