<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

use Amasty\ImageOptimizer\Model\Image\ReplaceAlgorithm\BestReplaceAlgorithm;
use Amasty\ImageOptimizer\Model\Image\ReplaceAlgorithm\PictureTagReplaceAlgorithm;
use Amasty\ImageOptimizer\Test\Integration\Model\Image\ReplaceAlgorithm\TestAlgorithm;
use Amasty\PageSpeedTools\Model\Image\ReplaceAlgorithmResolver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->configure([
    ReplaceAlgorithmResolver::class => [
        'arguments' => [
            'replaceAlgorithms' => [
                'replace_with_best' => [
                    'instance' => BestReplaceAlgorithm::class // @phpstan-ignore class.notFound
                ],
                'replace_with_picture' => [
                    'instance' => PictureTagReplaceAlgorithm::class // @phpstan-ignore class.notFound
                ],
                'replace_with_test' => [
                    'instance' => TestAlgorithm::class
                ]
            ]
        ]
    ]
]);
