<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Observer;

use Amasty\Base\Model\Di\Wrapper as CatalogMediaConfig;
use Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\QueryParamsOptimization;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OptimizeImages implements ObserverInterface
{
    // this constant exists in \Magento\Catalog\Model\Config\CatalogMediaConfig since Magento 2.4 version.
    public const IMAGE_OPTIMIZATION_PARAMETERS = 'image_optimization_parameters';

    /**
     * @var State
     */
    private $state;

    /**
     * @var CatalogMediaConfig
     */
    private $catalogMediaConfig;

    /**
     * @var QueryParamsOptimization
     */
    private $queryParamsOptimization;

    public function __construct(
        State $state,
        CatalogMediaConfig $catalogMediaConfig,
        QueryParamsOptimization $queryParamsOptimization
    ) {
        $this->state = $state;
        $this->catalogMediaConfig = $catalogMediaConfig;
        $this->queryParamsOptimization = $queryParamsOptimization;
    }

    public function execute(Observer $observer): void
    {
        $catalogMediaUrlFormat = $this->catalogMediaConfig->getMediaUrlFormat();
        if ($catalogMediaUrlFormat !== self::IMAGE_OPTIMIZATION_PARAMETERS
            || $this->state->isAreaCodeEmulated()
        ) {
            return;
        }

        $product = $observer->getEvent()->getProduct();
        if ($product->getId()) {
            $this->queryParamsOptimization->execute($product);
        }
    }
}
