<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Olegnax\InstagramFeedPro\Model\Config\Source\MediaType as Collection;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Edit extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsPost_view';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var array
     */
    protected $items;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param Collection $collection
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        Collection $collection
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->items = $collection->toArray();
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
        $id = $this->getRequest()->getParam('intspost_id');
        $model = $this->_objectManager->create(IntsPost::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Post no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('olegnax_instagramfeedpro_intspost', $model);

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Post') : __('New Post'),
            $id ? __('Edit Post') : __('New Post')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId()
                ? __(
                'Edit %1 Post %2',
                $this->getMediaType($model->getMediaType()),
                $model->getIntsId()
            )
                : __('New Post')
        );
        return $resultPage;
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function getMediaType($identifier)
    {
        return array_key_exists($identifier, $this->items) ? $this->items[$identifier] : $identifier;
    }
}
