<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Setup\Patch\Data;

use Amasty\ShopbyBase\Helper\FilterSetting;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class FillAttributeCodeColumn implements DataPatchInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return DataPatchInterface
     */
    public function apply()
    {
        $filterSettingCollection = $this->collectionFactory->create();
        foreach ($filterSettingCollection as $filterSetting) {
            $code = substr($filterSetting->getFilterCode(), 0, strlen(FilterSetting::ATTR_PREFIX))
                ? substr($filterSetting->getFilterCode(), strlen(FilterSetting::ATTR_PREFIX))
                : $filterSetting->getFilterCode();
            $filterSetting->setAttributeCode($code);
        }
        $filterSettingCollection->save();

        return $this;
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
