<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save\Preprocessors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Save\DataPreprocessorInterface;
use Amasty\Label\Model\ScopeDateValidator;
use Magento\Framework\Stdlib\DateTime as StdlibDate;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;

class FilterActiveFromToDates implements DataPreprocessorInterface
{
    /**
     * @var DateTime
     */
    private $dateFilter;

    /**
     * @var ScopeDateValidator
     */
    private $scopeDateValidator;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        DateTime $dateFilter,
        ScopeDateValidator $scopeDateValidator,
        TimezoneInterface $timezone
    ) {
        $this->dateFilter = $dateFilter;
        $this->scopeDateValidator = $scopeDateValidator;
        $this->timezone = $timezone;
    }

    public function process(array $data): array
    {
        if (!empty($data[LabelInterface::ACTIVE_FROM]) || !empty($data[LabelInterface::ACTIVE_TO])) {
            $data[LabelInterface::STATUS] = $this->scopeDateValidator->isScopeDateInInterval(
                Store::DEFAULT_STORE_ID,
                $this->getDateInTimezone($data, LabelInterface::ACTIVE_FROM),
                $this->getDateInTimezone($data, LabelInterface::ACTIVE_TO)
            );
        }

        return $data;
    }

    /**
     * Retrieve from array and convert date to default timezone.
     */
    private function getDateInTimezone(array $data, string $key): ?string
    {
        if (!empty($data[$key])) {
            $dateValue = $this->timezone->scopeDate(null, $data[$key], true)
                ->format(StdlibDate::DATETIME_PHP_FORMAT);
        } else {
            $dateValue = null;
        }

        return $dateValue;
    }
}
