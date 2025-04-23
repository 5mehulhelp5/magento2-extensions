<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollector;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\Repository\LinkRepository;

class TreePath implements DataCollectorInterface
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

    public function execute(array $data): array
    {
        $parent = $this->getParentLink($data);
        $data[LinkInterface::LEVEL] = $parent
            ? $parent->getLevel() + LinkInterface::LEVEL_STEP
            : LinkInterface::DEFAULT_LEVEL;
        $data[LinkInterface::PATH] = $parent
            ? $parent->getPath() . $parent->getEntityId() . LinkInterface::PATH_SEPARATOR
            : LinkInterface::DEFAULT_PATH;

        return $data;
    }

    private function getParentLink(array $data): ?LinkInterface
    {
        return $this->isParentExist($data) ? $this->linkRepository->getById($data[LinkInterface::PARENT_ID]) : null;
    }

    private function isParentExist(array $data): bool
    {
        return !empty($data[LinkInterface::PARENT_ID])
            && $this->linkRepository->isEntityExist((int) $data[LinkInterface::PARENT_ID]);
    }
}
