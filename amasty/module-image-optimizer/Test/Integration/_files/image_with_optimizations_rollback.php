<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

use Amasty\PageSpeedTools\Model\OptionSource\Resolutions;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Filesystem $filesystem */
$filesystem = $objectManager->create(Filesystem::class);
$mediaWriter = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
$webpPath = $mediaWriter->getAbsolutePath(Resolutions::WEBP_DIR);
$mobilePath = $mediaWriter->getAbsolutePath(Resolutions::RESOLUTIONS[Resolutions::MOBILE]['dir']);
$tabletPath = $mediaWriter->getAbsolutePath(Resolutions::RESOLUTIONS[Resolutions::TABLET]['dir']);

$mediaWriter->delete($mediaWriter->getAbsolutePath() . 'test_image.jpg');
$mediaWriter->delete($webpPath . 'test_image_jpg.webp');
$mediaWriter->delete($mobilePath . 'test_image_jpg.webp');
$mediaWriter->delete($tabletPath . 'test_image_jpg.webp');
