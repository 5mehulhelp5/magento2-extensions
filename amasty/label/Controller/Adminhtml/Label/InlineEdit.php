<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Controller\Adminhtml\Label;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\LabelRepositoryInterface;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Label\Model\ResourceModel\Label\Grid\Collection as FlatCollection;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->labelRepository = $labelRepository;

        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);

        if ($this->getRequest()->getParam('isAjax') && !empty($postItems)) {
            foreach ($postItems as $labelId => $labelData) {
                try {
                    $this->processSave((int) $labelId, $labelData);
                } catch (LocalizedException $e) {
                    $messages[] = $e->getMessage();
                    $error = true;
                } catch (RuntimeException $e) {
                    $messages[] = $e->getMessage();
                    $error = true;
                } catch (Exception $e) {
                    $messages[] = __('Something went wrong while saving the label.');
                    $error = true;
                }
            }
        } else {
            $messages[] = __('Please correct the data sent.');
            $error = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    private function processSave(int $labelId, array $labelData): void
    {
        $label = $this->labelRepository->getById($labelId);

        foreach ($label->getExtensionAttributes()->getFrontendSettings() as $frontendSetting) {
            $prefix = $frontendSetting->getType() === Collection::MODE_LIST
                ? FlatCollection::CATEGORY_PREFIX
                : FlatCollection::PRODUCT_PREFIX;
            $positionKey = $prefix . '_' . LabelFrontendSettingsInterface::POSITION;
            if (isset($labelData[$positionKey])) {
                $frontendSetting->setPosition((int)$labelData[$positionKey]);
            }
        }

        if (array_key_exists(LabelInterface::NAME, $labelData)) {
            $label->setName((string) $labelData[LabelInterface::NAME]);
        }

        if (array_key_exists(LabelInterface::STATUS, $labelData)) {
            $label->setStatus((int) $labelData[LabelInterface::STATUS]);
        }

        $this->labelRepository->save($label);
    }
}
