<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizerUi\Controller\Adminhtml\Image\Folders;
use Amasty\ImageOptimizerUi\Model\Image\ResourceModel\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

/**
 * @api
 */
class ImageSettingGetter
{
    /**
     * @var CollectionFactory
     */
    private $imageCollectionFactory;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var ImageSettingInterface|null
     */
    private $suitableImageSetting = null;

    public function __construct(
        CollectionFactory $imageCollectionFactory,
        Filesystem $filesystem
    ) {
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * To retrieve more suitable optimization pattern this method uses the uploaded image path and compares that with
     * selected 'Folders for Optimization' in all patterns. The most suitable pattern has three directories in the
     * uploaded image path.
     */
    public function getSettingByImgPath(string $imgPath): ?ImageSettingInterface
    {
        if (null !== $this->suitableImageSetting) {
            return $this->suitableImageSetting;
        }

        $relativePath = $this->mediaDirectory->getRelativePath($imgPath);
        $arrRelativePath = explode('/', $relativePath);
        $needlePaths = $this->prepareImagePathsVariations($arrRelativePath);

        $imagesSetting = [];
        $imageSettingCollection = $this->imageCollectionFactory->create();
        foreach ($imageSettingCollection->getItems() as $imageSetting) {
            foreach ($imageSetting->getFolders() as $folder) {
                if ($i = $this->pathFinder($needlePaths, $folder)) {
                    $imagesSetting[$i] = $imageSetting;
                }
            }
        }

        if (!empty($imagesSetting)) {
            $this->suitableImageSetting = $imagesSetting[max(array_keys($imagesSetting))];
        }

        return $this->suitableImageSetting;
    }

    /**
     * @param string[] $arrRelativePath
     */
    private function prepareImagePathsVariations(array $arrRelativePath): array
    {
        $needlePaths = [];
        for ($folderLevel = Folders::FOLDER_MAX_DEPTH_LEVEL; $folderLevel > 0; $folderLevel--) {
            $path = array_slice($arrRelativePath, 0, $folderLevel);
            $needlePaths[$folderLevel] = implode('/', $path);
        }

        return array_unique($needlePaths);
    }

    /**
     * @param string[] $needlePaths
     */
    private function pathFinder(array $needlePaths, string $folder): int
    {
        for ($folderLevel = Folders::FOLDER_MAX_DEPTH_LEVEL; $folderLevel > 0; $folderLevel--) {
            if (isset($needlePaths[$folderLevel]) && strcmp($needlePaths[$folderLevel], $folder) === 0) {
                return $folderLevel;
            }
        }

        return 0;
    }
}
