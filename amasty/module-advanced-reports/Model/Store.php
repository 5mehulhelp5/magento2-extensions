<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model;

use Magento\Backend\Model\Session;
use Amasty\Reports\Model\ResourceModel\Filters\RequestFiltersProvider;

class Store
{
    public const SESSION_KEY = 'amreports_store';

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestFiltersProvider
     */
    private $requestFiltersProvider;

    public function __construct(
        Session $session,
        RequestFiltersProvider $requestFiltersProvider
    ) {
        $this->session = $session;
        $this->requestFiltersProvider = $requestFiltersProvider;
    }

    /**
     * @return int
     */
    public function getCurrentStoreId(): int
    {
        $params = $this->requestFiltersProvider->execute();
        $storeId = $params[RequestFiltersProvider::REPORTS_KEY]['store'] ?? $params['store'] ?? null;
        if ($storeId === null) {
            $storeId = $this->session->getData(self::SESSION_KEY, false);
        } else {
            if ((int)$storeId) {
                $this->setCurrentStore((int)$storeId);
            } else {
                // if $storeId === 0 - clear data from session
                $this->session->getData(self::SESSION_KEY, true);
            }
        }

        return (int)$storeId;
    }

    /**
     * @param int $storeId
     * @return Session
     */
    public function setCurrentStore(int $storeId): Session
    {
        return $this->session->setAmreportsStore($storeId);
    }
}
