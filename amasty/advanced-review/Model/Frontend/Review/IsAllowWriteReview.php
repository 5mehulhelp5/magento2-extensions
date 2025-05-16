<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model\Frontend\Review;

use Amasty\AdvancedReview\Model\ConfigProvider;
use Amasty\AdvancedReview\Model\Sources\WhoCanSubmit;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;

class IsAllowWriteReview
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerSessionFactory
     */
    private $customerSessionFactory;

    /**
     * @var CustomerSession|null
     */
    private $customerSession;

    public function __construct(
        ConfigProvider $configProvider,
        CustomerSessionFactory $customerSessionFactory
    ) {
        $this->configProvider = $configProvider;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    public function execute(): bool
    {
        return $this->configProvider->getWhoCanSubmit() === WhoCanSubmit::ALL
            || $this->getCustomerSession()->getCustomerId();
    }

    private function getCustomerSession(): CustomerSession
    {
        if ($this->customerSession === null) {
            $this->customerSession = $this->customerSessionFactory->create();
        }

        return $this->customerSession;
    }
}
