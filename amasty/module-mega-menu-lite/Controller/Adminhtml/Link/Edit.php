<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Link;

use Amasty\MegaMenuLite\Api\LinkRepositoryInterface;
use Amasty\MegaMenuLite\Model\Backend\DataProvider\LinkRegistry;
use Amasty\MegaMenuLite\Model\Menu\Link;
use Amasty\MegaMenuLite\Model\Menu\LinkFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LinkRepositoryInterface
     */
    private $linkRepository;

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var LinkRegistry
     */
    private $registry;

    public function __construct(
        Action\Context $context,
        LinkRepositoryInterface $linkRepository,
        LinkFactory $linkFactory,
        DataPersistorInterface $dataPersistor,
        LinkRegistry $registry
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->linkRepository = $linkRepository;
        $this->linkFactory = $linkFactory;
        $this->registry = $registry;
    }

    /**
     * @return Redirect|ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $linkId = (int)$this->getRequest()->getParam('id');
        if ($linkId) {
            try {
                $model = $this->linkRepository->getById($linkId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Custom Menu Item no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/index');
            }
        } else {
            /** @var Link $model */
            $model = $this->linkFactory->create();
        }

        // set entered data if was error when we do save
        $data = $this->dataPersistor->get(Link::PERSIST_NAME);
        if (!empty($data) && !$model->getEntityId()) {
            $model->addData($data);
        }

        $this->registry->registerLink($model);
        $this->registry->registerStoreId((int) $this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID));

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $text = $model->getEntityId() ?
            __('Edit Custom Menu Item # %1', $model->getEntityId())
            : __('New Custom Menu Item');
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend($text);

        return $resultPage;
    }
}
