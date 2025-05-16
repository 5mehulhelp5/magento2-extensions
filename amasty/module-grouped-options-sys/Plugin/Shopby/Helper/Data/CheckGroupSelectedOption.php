<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Plugin\Shopby\Helper\Data;

use Amasty\Base\Model\Di\Wrapper as DiWrapper;
use Amasty\GroupedOptions\Model\GroupAttr\GetFakeKeyByCode;
use Amasty\Shopby\Helper\Data as Helper;
use Amasty\Shopby\Model\Layer\Filter\Item as FilterItem;
use Amasty\Shopby\Model\Request;

class CheckGroupSelectedOption
{
    /**
     * @var GetFakeKeyByCode
     */
    private $getFakeKeyByCode;

    /**
     * @var Request
     */
    private $shopbyRequest;

    public function __construct(GetFakeKeyByCode $getFakeKeyByCode, DiWrapper $shopbyRequest)
    {
        $this->getFakeKeyByCode = $getFakeKeyByCode;
        $this->shopbyRequest = $shopbyRequest;
    }

    /**
     * isFilterItemSelected convert option ids to int before check.
     * Need group options separately, because they have string value.
     *
     * @see \Amasty\Shopby\Helper\Data::isFilterItemSelected
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsFilterItemSelected(Helper $helper, int $result, FilterItem $filterItem): int
    {
        if (!$result
            && is_string($filterItem->getValue())
            && $filterItem->getFilter()->hasAttributeModel()
            && $data = $this->shopbyRequest->getFilterParam($filterItem->getFilter())
        ) {
            $values = explode(',', $data);
            $result = (int) ($this->getFakeKeyByCode->execute(
                (int) $filterItem->getFilter()->getAttributeModel()->getAttributeId(),
                $filterItem->getValue()
            ) !== null && in_array($filterItem->getValue(), $values));
        }
        return $result;
    }
}
