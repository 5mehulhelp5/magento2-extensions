<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\Data;

use Amasty\Label\Model\Indexer\LabelIndexer;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class InvalidateLabelIndex implements DataPatchInterface, NonTransactionableInterface
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

    public static function getDependencies(): array
    {
        return [
            DeployLabelExamples::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): InvalidateLabelIndex
    {
        $this->labelIndexer->invalidateIndex();

        return $this;
    }
}
