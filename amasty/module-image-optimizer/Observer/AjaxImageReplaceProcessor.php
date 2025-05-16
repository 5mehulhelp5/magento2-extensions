<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Observer;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ImageReplaceProcessor;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfig;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig\ReplaceConfigFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AjaxImageReplaceProcessor implements ObserverInterface
{
    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var ImageReplaceProcessor
     */
    private $imageReplaceProcessor;

    /**
     * @var ReplaceConfig
     */
    private $replaceConfig;

    public function __construct(
        LazyConfigProvider $lazyConfigProvider,
        ImageReplaceProcessor $imageReplaceProcessor,
        ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->imageReplaceProcessor = $imageReplaceProcessor;
        $this->lazyConfigProvider = $lazyConfigProvider;
        $this->replaceConfig = $replaceConfigFactory->create();
    }

    public function execute(Observer $observer): void
    {
        $data = $observer->getData('data');
        if ($data && $data->hasData('page')) {
            if ($this->isInvalidByModuleConfig() && $this->isInvalidByLazyConfig($data)) {
                return;
            }

            $page = $data->getData('page');
            $this->imageReplaceProcessor->processImages($page);
            $data->setData('page', $page);
        }
    }

    private function isInvalidByModuleConfig(): bool
    {
        $replaceStrategy = $this->replaceConfig->getData(ReplaceConfig::REPLACE_STRATEGY);

        return $this->lazyConfigProvider->get()->getData('is_lazy') || $replaceStrategy === ReplaceStrategies::NONE;
    }

    private function isInvalidByLazyConfig(DataObject $data): bool
    {
        $lazyConfig = $data->hasData('lazyConfig') ? $data->getData('lazyConfig') : [];

        return $lazyConfig['is_lazy'] ?? false;
    }
}
