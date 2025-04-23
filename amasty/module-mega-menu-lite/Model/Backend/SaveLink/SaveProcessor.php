<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink;

use Amasty\MegaMenuLite\Model\Backend\SaveLink\Processor\SaveItem;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\Processor\SaveLink;

class SaveProcessor
{
    /**
     * @var Pool
     */
    private $dataCollector;

    /**
     * @var SaveLink
     */
    private $saveLink;

    /**
     * @var SaveItem
     */
    private $saveItem;

    public function __construct(
        Pool $dataCollector,
        SaveLink $saveLink,
        SaveItem $saveItem
    ) {
        $this->dataCollector = $dataCollector;
        $this->saveLink = $saveLink;
        $this->saveItem = $saveItem;
    }

    public function execute(array $inputData): int
    {
        $inputData = $this->dataCollector->execute($inputData);
        $linkEntityId = $this->saveLink->execute($inputData);
        $this->saveItem->execute($linkEntityId, $inputData);

        return $linkEntityId;
    }
}
