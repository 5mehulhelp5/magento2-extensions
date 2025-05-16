<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model;

use Amasty\XnotifSubscriptionFunctionality\Api\Data\StockAlertInterface;
use Amasty\XnotifSubscriptionFunctionality\Api\StockAlertRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;

class StockAlertResolver
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string[]
     */
    private $paramsToCheck;

    public function __construct(
        ConfigProvider $configProvider,
        RequestInterface $request,
        array $paramsToCheck = []
    ) {
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->paramsToCheck = $paramsToCheck;
    }

    public function execute(): bool
    {
        $isRestock = false;

        if (!$this->configProvider->isRestockAlertEnabled()) {
            return false;
        }

        foreach ($this->paramsToCheck as $param) {
            if (!!$this->request->getParam($param, false)) {
                $isRestock = true;
                break;
            }
        }

        return $isRestock;
    }
}
