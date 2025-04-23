<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save\Adminhtml;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Api\Data\LabelInterfaceFactory;
use Amasty\Label\Api\LabelRepositoryInterface;
use Amasty\Label\Model\Label\Parts\Factory as PartFactory;
use Amasty\Label\Model\Label\Parts\MetaProvider;
use Amasty\Label\Model\Label\Save\DataPreprocessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ObjectManager;

class SaveFromEditForm
{
    /**
     * @var DataPreprocessorInterface
     */
    private $dataPreprocessor;

    /**
     * @var LabelInterfaceFactory
     */
    private $labelFactory;

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    public function __construct(
        DataPreprocessorInterface $dataPreprocessor,
        ?MetaProvider $metaProvider, // @deprecated
        LabelInterfaceFactory $labelFactory,
        LabelRepositoryInterface $labelRepository,
        ?PartFactory $partFactory, // @deprecated
        DataObjectHelper $dataObjectHelper = null // TODO move to not optional
    ) {
        $this->dataPreprocessor = $dataPreprocessor;
        $this->labelFactory = $labelFactory;
        $this->labelRepository = $labelRepository;
        $this->dataObjectHelper = $dataObjectHelper ?? ObjectManager::getInstance()->get(DataObjectHelper::class);
    }

    public function execute(array $postData): LabelInterface
    {
        $processedData = $this->dataPreprocessor->process($postData);
        $label = $this->labelFactory->create();
        $label->setData($processedData);

        $this->dataObjectHelper->populateWithArray($label, $processedData, LabelInterface::class);

        $this->labelRepository->save($label);

        return $label;
    }
}
