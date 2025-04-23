<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\CatalogRule\Model\Indexer;

use Amasty\Label\Model\Indexer\LabelMainIndexer;
use Amasty\Label\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Fix for magento 2.4.3 version and lower
 * because magento updates catalog price rule without running indexer
 */
class PartialIndex
{
    /**
     * @var LabelMainIndexer
     */
    private $indexer;

    /**
     * @var LabelCollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LabelMainIndexer $indexer,
        LabelCollectionFactory $labelCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->indexer = $indexer;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->logger = $logger;
    }

    public function afterPartialUpdateCatalogRuleProductPrice(
        \Magento\CatalogRule\Model\Indexer\PartialIndex $subject //@phpstan-ignore class.notFound
    ): void {
        $labelIds = $this->getIdsForReindex();

        if (!empty($labelIds)) {
            try {
                $this->indexer->executeByLabelIds($labelIds);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    private function getIdsForReindex(): array
    {
        $collection = $this->labelCollectionFactory->create();
        $collection->addActiveFilter();
        $collection->addIsSaleFilterApplied();

        return array_map('intval', $collection->getAllIds());
    }
}
