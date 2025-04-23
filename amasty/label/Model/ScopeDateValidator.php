<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model;

use Magento\Framework\App\ScopeInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\App\ScopeResolverInterface;

class ScopeDateValidator
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var Timezone
     */
    private $timezone;

    public function __construct(
        DateTime $dateTime,
        ScopeResolverInterface $scopeResolver,
        Timezone $timezone
    ) {
        $this->dateTime = $dateTime;
        $this->scopeResolver = $scopeResolver;
        $this->timezone = $timezone;
    }

    /**
     * @param int $scope
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return bool
     */
    public function isScopeDateInInterval(
        int $scope,
        ?string $dateFrom = null,
        ?string $dateTo = null
    ): bool {
        $scope = $this->scopeResolver->getScope($scope);
        $scopeTimeStamp = $this->timezone->scopeTimeStamp($scope);
        $fromTimeStamp = $dateFrom ? strtotime($dateFrom) : 0;
        $toTimeStamp = $dateTo ? strtotime($dateTo) : 0;

        return ($this->dateTime->isEmptyDate($dateFrom) || $scopeTimeStamp > $fromTimeStamp) &&
            ($this->dateTime->isEmptyDate($dateTo) || $scopeTimeStamp < $toTimeStamp);
    }
}
