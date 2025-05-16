<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\RemoteStorage\Driver\DriverPool as RemoteDriverPool;

class ImageHelper
{
    public const IMAGE_PATH = '/amasty/review/';
    public const IMAGE_TMP_PATH = '/amasty/review/tmp/';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $fileManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Filesystem\Io\File $fileManager
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->fileManager = $fileManager;
    }

    /**
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFullPath($name)
    {
        $path = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $path = trim($path, '/');
        $name = trim($name, '/');
        $path .= '/amasty/review/';

        return $path . $name;
    }

    /**
     * @param $image
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function resize($image, $width = null, $height = null)
    {
        $image = ltrim($image, '/');
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(
            UrlInterface::URL_TYPE_MEDIA
        );
        $mediaUrl = trim($mediaUrl, '/');
        $resizedURL = $mediaUrl . self::IMAGE_PATH . 'resized/' . $width . '/' . $image;

        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath(self::IMAGE_PATH) . $image;
        $imageResized = $mediaDirectory->getAbsolutePath(self::IMAGE_PATH . 'resized/' . $width . '/')
            . $image;

        if ($mediaDirectory->isExist($imageResized) && $mediaDirectory->isFile($imageResized)) {
            // if resized image already exists
            return $resizedURL;
        }

        if (!$mediaDirectory->isExist($absolutePath) || !$mediaDirectory->isFile($absolutePath)) {
            // if the original image doesn't exist, or it's not a file
            return '';
        }

        //create image factory...
        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);

        $destination = $imageResized;
        $imageResize->save($destination);

        return $resizedURL;
    }
}
