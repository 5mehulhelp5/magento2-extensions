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

$img = imagecreatetruecolor(300, 300);
$color = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, 300, 300, $color);

imagejpeg($img, $mediaWriter->getAbsolutePath() . "test_image.jpg", 100);

$mediaWriter->create($webpPath);
if (function_exists('imagewebp')) {
    imagewebp($img, $webpPath . "test_image_jpg.webp", 100);
} else {
    touch($webpPath . "test_image_jpg.webp");
}

$mediaWriter->create($mobilePath);
imagejpeg($img, $mobilePath . "test_image.jpg", 100);

$mediaWriter->create($tabletPath);
imagejpeg($img, $tabletPath . "test_image.jpg", 100);
