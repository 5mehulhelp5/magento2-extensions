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
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Olegnax\InstagramFeedPro\Model\Data\IntsPost as DataIntsPost;
use Olegnax\InstagramFeedPro\Model\IntsPost;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsPost_save';

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
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('intspost_id');

            $model = $this->_objectManager->create(IntsPost::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Post no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);
            // Save Related
            $data = $this->getRequest()->getPost('data');

            $_related = [];
            $_hotspot = [];
            $links = isset($data['links']) ? $data['links'] : [
                'product' => [],
                'hotspot' => [],
            ];
            if (is_array($links)) {
                if (isset($links['product']) && is_array($links['product'])) {
                    foreach ($links['product'] as $item) {
                        $item = [
                            'entity_id' => (int)$item['id'],
                            'position' => (int)$item['position'],
                            'marker_style' => $item['marker_style'] ?: '',
                            'position_top' => (float)str_replace(',', '.', (string)$item['position_top']),
                            'position_left' => (float)str_replace(',', '.', (string)$item['position_left']),
                            'image_index' => (int)$item['image_index'],
                        ];

                        $minPos = empty($item['marker_style']) ? '' : 0;
                        if (0 >= $item['position_top']) {
                            $item['position_top'] = $minPos;
                        }
                        if (0 >= $item['position_left']) {
                            $item['position_left'] = $minPos;
                        }

                        $_related[$item['entity_id']] = $item;
                    }
                }

                if (isset($links['hotspot']) && is_array($links['hotspot'])) {
                    foreach ($links['hotspot'] as $item) {
                        $item = [
                            'hotspot_id' => (int)$item['id'],
                            'position' => (int)$item['position'],
                            'position_top' => (float)str_replace(',', '.', (string)$item['position_top']),
                            'position_left' => (float)str_replace(',', '.', (string)$item['position_left']),
                            'image_index' => (int)$item['image_index'],
                        ];

                        if (0 >= $item['position_top']) {
                            $item['position_top'] = '';
                        }
                        if (0 >= $item['position_left']) {
                            $item['position_left'] = '';
                        }
                        $_hotspot[$item['hotspot_id']] = $item;
                    }
                }
            }

            $model->setData(DataIntsPost::RELATED, $_related);
            $model->setData(DataIntsPost::HOTSPOT, $_hotspot);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Post.'));
                $this->dataPersistor->clear('olegnax_instagramfeedpro_intspost');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['intspost_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Post.'));
            }

            $this->dataPersistor->set('olegnax_instagramfeedpro_intspost', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'intspost_id' => $this->getRequest()->getParam('intspost_id'),
                ]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
