<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Helper\ImageHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader;

class ImageUploader
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile = null // @deprecated backward compatibility
    ) {
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->adapterFactory = $adapterFactory;
    }

    public function execute(array $file, $isTmp = false)
    {
        $mediaDirectoryWrite = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $path = rtrim($mediaDirectoryWrite->getAbsolutePath(
            $isTmp ? ImageHelper::IMAGE_TMP_PATH : ImageHelper::IMAGE_PATH
        ), '/');

        if (!$mediaDirectoryWrite->isExist($path) || !$mediaDirectoryWrite->isDirectory($path)) {
            $mediaDirectoryWrite->getDriver()->createDirectory($path);
        }

        /** @var $uploader Uploader */
        $uploader = $this->fileUploaderFactory->create(
            ['fileId' => $file]
        );
        $imageAdapter = $this->adapterFactory->create();
        $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        return $uploader->save($path);
    }

    public function copy(string $imagePath)
    {
        $imagePath = trim($imagePath, '/');
        $mediaDirectory = $this->filesystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $path = $mediaDirectory->getAbsolutePath(
            ImageHelper::IMAGE_TMP_PATH
        );

        $from = $path . $imagePath;

        if ($mediaDirectory->isExist($from)) {
            $realPath = $mediaDirectory->getAbsolutePath(
                ImageHelper::IMAGE_PATH
            );

            $counter = 0;
            while ($mediaDirectory->isFile($realPath . $imagePath)) {
                $imagePathArray = explode('.', $imagePath);
                $imagePathArray[0] .= $counter++;
                $imagePath = implode('.', $imagePathArray);
            }

            if ($mediaDirectory->copyFile($from, $realPath . $imagePath)) {
                return $imagePath;
            }
        }

        return false;
    }
}
