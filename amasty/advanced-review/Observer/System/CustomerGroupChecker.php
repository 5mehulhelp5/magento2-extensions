<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\System;

use Amasty\AdvancedReview\Helper\Config;

class CustomerGroupChecker
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $groups = [];

    /**
     * ConfigProvider constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $groups
     * @return CustomerGroupChecker
     */
    public function setCustomerGroup(array $groups): CustomerGroupChecker
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Is changed customer group during system config edition
     *
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->groups != $this->config->getCustomerGroups();
    }
}
