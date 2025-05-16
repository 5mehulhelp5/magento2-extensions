<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\ProductAlert\Controller;

use Amasty\Xnotif\Model\Notification\DefaultAlert\Url\UnsubscribeParameterProcessor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider;

class Unsubscribe
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider
     */
    private $alertProvider;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var UnsubscribeParameterProcessor
     */
    private $unsubscribeParameterProcessor;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Amasty\Xnotif\Model\ResourceModel\Unsubscribe\AlertProvider $alertProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        UnsubscribeParameterProcessor $unsubscribeParameterProcessor
    ) {
        $this->request = $request;
        $this->alertProvider = $alertProvider;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->unsubscribeParameterProcessor = $unsubscribeParameterProcessor;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        $productId = $this->request->getParam('product_id', $this->request->getParam('product'));
        if ($request->getActionName() == 'stockAll') {
            $productId = AlertProvider::REMOVE_ALL;
        }

        $type = $this->request->getParam('type', $this->request->getActionName());
        try {
            $subscribeConditions = $this->unsubscribeParameterProcessor->decode($this->request->getParam('hash', ''));
            $collection = $this->alertProvider->getAlertModel($type, $productId, $subscribeConditions);
            if ($collection && $collection->getSize()) {
                $collection->walk('delete');
            }
            if ($productId == AlertProvider::REMOVE_ALL) {
                $this->messageManager->addSuccessMessage(
                    __('You will no longer receive stock alerts.')
                );
            } else {
                $this->messageManager->addSuccessMessage($this->getSuccessMessage($type));
            }
        } catch (\Exception $ex) {
            $this->messageManager->addErrorMessage(__('The product was not found.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/');

        return $resultRedirect;
    }

    private function getSuccessMessage(string $type): string
    {
        switch ($type) {
            case AlertProvider::PRICE_TYPE:
                $message = __('You will no longer receive price alerts for this product.');
                break;
            default:
                $message = __('You will no longer receive stock alerts for this product.');
                break;
        }

        return $message;
    }
}
