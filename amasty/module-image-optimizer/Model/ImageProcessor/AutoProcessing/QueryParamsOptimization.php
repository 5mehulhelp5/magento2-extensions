<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing;

use Magento\Catalog\Model\Product\Media\ConfigInterface as MediaConfig;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Catalog\Api\Data\ProductInterface;

class QueryParamsOptimization
{
    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    /**
     * @var MediaConfig
     */
    private $imageConfig;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    public function __construct(
        ProcessorsProvider $processorsProvider,
        MediaConfig $imageConfig,
        Filesystem $filesystem
    ) {
        $this->processorsProvider = $processorsProvider;
        $this->imageConfig = $imageConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function execute(ProductInterface $product): void
    {
        $originalImages = $product->getOrigData('media_gallery');
        $updatedImages = $product->getData('media_gallery');
        $mediaGallery = !empty($updatedImages['images']) ? array_column($updatedImages['images'], 'file') : [];
        $mediaOriginalGallery = !empty($originalImages['images'])
            ? array_column($originalImages['images'], 'file')
            : [];
        $this->processImages(array_diff($mediaGallery, $mediaOriginalGallery));
    }

    private function processImages(array $imagePaths): void
    {
        foreach ($imagePaths as $path) {
            $mediaStorageFilename = $this->imageConfig->getMediaPath($path);
            $originalImagePath = $this->mediaDirectory->getAbsolutePath($mediaStorageFilename);
            foreach ($this->processorsProvider->getAll() as $processor) {
                $processor->execute($originalImagePath);
            }
        }
    }
}
