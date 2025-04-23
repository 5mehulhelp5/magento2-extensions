<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Model\Indexer\LabelMainIndexer;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\ResourceModel\Mview\StateManager;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class CommentVisibility implements ModifierInterface
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        StateManager $stateManager,
        IndexerRegistry $indexerRegistry,
        LabelRegistry $labelRegistry
    ) {
        $this->stateManager = $stateManager;
        $this->indexerRegistry = $indexerRegistry;
        $this->labelRegistry = $labelRegistry;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $label = $this->labelRegistry->getCurrentLabel();
        if ($label
            && $label->getLabelId()
            && $this->indexerRegistry->get(LabelMainIndexer::INDEXER_ID)->isScheduled()
            && $this->stateManager->isScheduled(
                LabelMainIndexer::INDEXER_ID,
                'amasty_label_main_cl',
                $label->getLabelId()
            )
        ) {
            $meta['product_conditions']['children']['preview_comment']['arguments']['data']['config']['visible'] = true;
        }

        return $meta;
    }
}
