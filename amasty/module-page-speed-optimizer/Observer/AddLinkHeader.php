<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Observer;

use Amasty\PageSpeedOptimizer\Model\Asset;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\HeaderProvider\HeaderLine\HeaderLineProcessorProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddLinkHeader implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var HeaderLineProcessorProvider
     */
    private $headerLineProcessorProvider;

    public function __construct(
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager = null, //@deprecated
        Asset\CollectorAdapter $collectorAdapter = null, //@deprecated
        HeaderLineProcessorProvider $headerLineProcessorProvider = null
    ) {
        $this->configProvider = $configProvider;
        $this->headerLineProcessorProvider = $headerLineProcessorProvider
            ?? ObjectManager::getInstance()->get(HeaderLineProcessorProvider::class);
    }

    public function execute(Observer $observer)
    {
        /** @var HttpInterface $response */
        $response = $observer->getResponse();

        $serverPushType = $this->configProvider->getServerPushType();
        $headerLineProcessor = $this->headerLineProcessorProvider->getProcessorByType($serverPushType);
        if ($response instanceof HttpInterface && null !== $headerLineProcessor) {
            $headerLine = $headerLineProcessor->getHeaderLine();
            if (!empty($headerLine)) {
                $response->setHeader('Link', $headerLine);
            }
        }
    }
}
