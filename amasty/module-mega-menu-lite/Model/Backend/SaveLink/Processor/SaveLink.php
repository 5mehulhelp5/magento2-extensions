<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\Processor;

use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Repository\LinkRepository;

class SaveLink
{
    /**
     * @var LinkRepository
     */
    private $linkRepository;

    public function __construct(
        LinkRepository $linkRepository
    ) {
        $this->linkRepository = $linkRepository;
    }

    public function execute(array $inputData): int
    {
        $entityId = (int) ($inputData[LinkInterface::ENTITY_ID] ?? 0);
        $link = $entityId ? $this->linkRepository->getById($entityId) : $this->linkRepository->getNew();
        $link->setData($inputData);
        $link = $this->linkRepository->save($link);

        return $link->getEntityId();
    }
}
