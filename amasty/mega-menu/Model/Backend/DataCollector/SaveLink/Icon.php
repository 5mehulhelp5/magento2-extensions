<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Backend\DataCollector\SaveLink;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\DataProvider\GetIconFromRequest;
use Amasty\MegaMenu\Model\Menu\GetImagePath;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;

class Icon implements DataCollectorInterface
{
    /**
     * @var GetImagePath
     */
    private $getImagePath;

    /**
     * @var GetIconFromRequest
     */
    private $getIconFromRequest;

    public function __construct(
        GetImagePath $getImagePath,
        GetIconFromRequest $getIconFromRequest
    ) {
        $this->getImagePath = $getImagePath;
        $this->getIconFromRequest = $getIconFromRequest;
    }

    public function execute(array $data): array
    {
        $data[ItemInterface::ICON] = $this->getImagePath->execute($this->getIconFromRequest->execute());

        return $data;
    }
}
