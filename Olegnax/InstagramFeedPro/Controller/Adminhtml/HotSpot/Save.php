<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\HotSpot;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Olegnax\InstagramFeedPro\Model\HotSpot;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::HotSpot_save';
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('hotspot_id');

            $model = $this->_objectManager->create(HotSpot::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Hotspot no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Hotspot.'));
                $this->dataPersistor->clear('olegnax_instagramfeedpro_hotspot');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['hotspot_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Hotspot.'));
            }

            $this->dataPersistor->set('olegnax_instagramfeedpro_hotspot', $data);
            return $resultRedirect->setPath('*/*/edit', ['hotspot_id' => $this->getRequest()->getParam('hotspot_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

