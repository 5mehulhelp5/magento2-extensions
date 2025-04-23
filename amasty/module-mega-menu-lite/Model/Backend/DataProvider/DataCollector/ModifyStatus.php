<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\DataProvider\DataCollector;

use Amasty\MegaMenuLite\Model\Backend\DataProvider\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;

/**
 * @deprecated
 * @see \Amasty\MegaMenuLite\Ui\DataProvider\Form\Link\Modifier\ModifyStatus
 */
class ModifyStatus implements DataCollectorInterface
{
    public function __construct(UrlKey $urlKey) // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    {
    }

    public function execute(array $data, int $storeId, int $entityId): array
    {
        return $data;
    }
}
