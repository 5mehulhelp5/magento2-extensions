<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report-api
 * @version   1.0.73
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Service;

use Mirasvit\ReportApi\Api\RequestInterface;

class StoreResolver
{
    /**
     * @var int|array|null
     */
    private static $storeId;

    /**
     * @param RequestInterface $request
     */
    public function registerRequest(RequestInterface $request)
    {
        foreach ($request->getFilters() as $filter) {
            if ($filter->getColumn() === 'sales_order|store_id') {
                self::$storeId = count($filter->getValue()) > 1 ? 0 : $filter->getValue();
            }
        }
    }

    /**
     * @return int|array|null
     */
    public function getStoreId()
    {
        return self::$storeId;
    }
}
