<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Api\Data\QueueInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;
use Amasty\ImageOptimizer\Model\ImageProcessor\Resolution\ResolutionProcessorApplier;
use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\Adapter\Gd2;

class Resolution implements ImageProcessorInterface
{
    /**
     * @var ResolutionProcessorApplier
     */
    private $resolutionProcessorApplier;

    public function __construct(
        ?Gd2 $gd2 = null, // @deprecated
        ?Filesystem $filesystem = null, // @deprecated
        ?CommandProvider $webpCommandProvider = null, // @deprecated
        ?ResolutionToolProvider $resolutionToolProvider = null, // @deprecated
        ResolutionProcessorApplier $resolutionProcessorApplier = null
    ) {
        $this->resolutionProcessorApplier = $resolutionProcessorApplier
            ?? ObjectManager::getInstance()->get(ResolutionProcessorApplier::class);
    }

    public function process(QueueInterface $queue): void
    {
        $this->resolutionProcessorApplier->apply($queue);
    }

    public function prepareQueue(string $file, ImageSettingInterface $imageSetting, QueueInterface $queue): bool
    {
        $resolutions = [];
        if ($imageSetting->isCreateMobileResolution()) {
            $resolutions[] = Resolutions::MOBILE;
        }
        if ($imageSetting->isCreateTabletResolution()) {
            $resolutions[] = Resolutions::TABLET;
        }
        $queue->setResolutions($resolutions);
        $queue->setResizeAlgorithm($imageSetting->getResizeAlgorithm());

        return !empty($resolutions);
    }
}
