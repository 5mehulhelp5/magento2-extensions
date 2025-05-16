<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Customer;

use Amasty\Xnotif\Model\Source\Group;
use Magento\Customer\Model\Session;

class GroupsValidator
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @param array $allowedGroups
     * @return bool
     */
    public function execute(array $allowedGroups): bool
    {
        return in_array(Group::ALL_GROUPS, $allowedGroups)
            || in_array($this->session->getCustomerGroupId(), $allowedGroups);
    }
}
