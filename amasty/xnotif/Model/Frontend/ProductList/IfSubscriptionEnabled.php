<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Frontend\ProductList;

use Amasty\Xnotif\Model\ConfigProvider;
use Amasty\Xnotif\Model\Source\Group;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Registry;

class IfSubscriptionEnabled
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(ConfigProvider $configProvider, HttpContext $httpContext, Registry $registry)
    {
        $this->configProvider = $configProvider;
        $this->httpContext = $httpContext;
        $this->registry = $registry;
    }

    public function execute(): bool
    {
        return $this->isCustomerGroupAllowed();
    }

    private function isCustomerGroupAllowed(): bool
    {
        $allowedGroups = $this->configProvider->getAllowedStockCustomerGroups();

        if (in_array(Group::ALL_GROUPS, $allowedGroups)) {
            return true;
        }

        return in_array($this->getCurrentCustomerGroupId(), $allowedGroups);
    }

    private function getCurrentCustomerGroupId(): int
    {
        return (int)$this->httpContext->getValue(Context::CONTEXT_GROUP);
    }
}
