<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\ResourceModel\Filters;

use Amasty\Reports\Model\Utilities\GetDefaultToDate;
use Amasty\Reports\Model\Utilities\GetLocalDate;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\DB\Select;

class AddToFilter
{
    /**
     * @var RequestFiltersProvider
     */
    private $filtersProvider;

    /**
     * @var GetDefaultToDate
     */
    private $getDefaultToDate;

    /**
     * @var GetLocalDate
     */
    private $getLocalDate;

    public function __construct(
        GetDefaultToDate $getDefaultToDate,
        GetLocalDate $getLocalDate,
        RequestFiltersProvider $filtersProvider,
        ?DateTime $dateTime = null // @deprecated
    ) {
        $this->filtersProvider = $filtersProvider;
        $this->getDefaultToDate = $getDefaultToDate;
        $this->getLocalDate = $getLocalDate;
    }

    /**
     * @param AbstractDb|Select  $object
     */
    public function execute(
        $object,
        string $dateFiled = 'created_at',
        string $tablePrefix = 'main_table',
        ?string $defaultTo = null,
        string $filterName = 'to'
    ): void {
        $filters = $this->filtersProvider->execute();
        if ($defaultTo !== null) {
            $to = $defaultTo;
        } else {
            if (isset($filters[$filterName])) {
                $to = $filters[$filterName];
                $this->getDefaultToDate->setDefaultValue($to);
            } else {
                $to = $this->getDefaultToDate->getDate();
            }
        }

        if ($to) {
            $to = $this->getLocalDate->execute($to);
            $select = $object instanceof Select ? $object : $object->getSelect();
            $select->where(sprintf('%s.%s <= ?', $tablePrefix, $dateFiled), $to);
        }
    }
}
