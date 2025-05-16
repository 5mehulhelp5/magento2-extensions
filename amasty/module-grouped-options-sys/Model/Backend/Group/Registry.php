<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Model\Backend\Group;

use Amasty\GroupedOptions\Api\Data\GroupAttrInterface;

class Registry
{
    /**
     * @var GroupAttrInterface|null
     */
    private $group;

    public function getGroup(): ?GroupAttrInterface
    {
        return $this->group;
    }

    public function setGroup(GroupAttrInterface $group): void
    {
        $this->group = $group;
    }
}
