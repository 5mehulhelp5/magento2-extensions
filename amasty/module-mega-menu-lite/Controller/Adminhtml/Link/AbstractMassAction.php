<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Controller\Adminhtml\Link;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\MegaMenuLite\Model\Repository\LinkRepository
     */
    protected $repository;

    /**
     * @var \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Amasty\MegaMenuLite\Model\Menu\LinkFactory
     */
    protected $modelFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \Amasty\MegaMenuLite\Model\Repository\LinkRepository $repository,
        \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\CollectionFactory $collectionFactory,
        \Amasty\MegaMenuLite\Model\Menu\LinkFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    /**
     * Execute action for group
     *
     * @param LinkInterface $link
     */
    abstract protected function itemAction(LinkInterface $link);

    /**
     * Mass action execution
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var \Amasty\MegaMenuLite\Model\ResourceModel\Menu\Link\Collection $collection */
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $model) {
                    $this->itemAction($model);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        //phpcs:ignore Magento2.Legacy.ObsoleteResponse.RedirectResponseMethodFound
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
