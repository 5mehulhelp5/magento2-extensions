<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;

class LazyConfigProvider
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var DataObject|null
     */
    private $lazyConfig;

    public function __construct(
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function get(): DataObject
    {
        if ($this->lazyConfig === null) {
            try {
                if ($this->moduleManager->isEnabled('Amasty_LazyLoad')) {
                    $this->lazyConfig = $this->objectManager->get(LazyConfig::class); // @phpstan-ignore class.notFound
                }
            } catch (\Throwable $e) {
                null;
            } finally {
                if ($this->lazyConfig === null) {
                    $this->lazyConfig = $this->dataObjectFactory->create();
                }
            }
        }

        return $this->lazyConfig;
    }
}
