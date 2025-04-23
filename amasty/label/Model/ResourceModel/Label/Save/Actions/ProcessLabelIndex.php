<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label\Save\Actions;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Indexer\LabelIndexer;
use Amasty\Label\Model\ResourceModel\Label\Save\AdditionalSaveActionInterface;

class ProcessLabelIndex implements AdditionalSaveActionInterface
{
    /**
     * @var LabelIndexer
     */
    private $labelIndexer;

    public function __construct(
        LabelIndexer $labelIndexer
    ) {
        $this->labelIndexer = $labelIndexer;
    }

    public function execute(LabelInterface $label): void
    {
        if (!$this->labelIndexer->isIndexerScheduled()) {
            $this->labelIndexer->executeByLabelId($label->getLabelId());
        }
    }
}
