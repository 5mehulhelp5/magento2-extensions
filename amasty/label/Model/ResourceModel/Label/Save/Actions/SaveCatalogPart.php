<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label\Save\Actions;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Label\Model\ResourceModel\Label\Save\AdditionalSaveActionInterface;

class SaveCatalogPart implements AdditionalSaveActionInterface
{
    /**
     * @var SaveExtensionAttributeAction
     */
    private $saveExtensionAttributeAction;

    public function __construct(
        SaveExtensionAttributeAction $saveExtensionAttributeAction
    ) {
        $this->saveExtensionAttributeAction = $saveExtensionAttributeAction;
    }

    public function execute(LabelInterface $label): void
    {
        $this->saveExtensionAttributeAction->execute($label);
    }
}
