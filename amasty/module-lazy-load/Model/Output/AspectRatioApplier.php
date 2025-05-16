<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Output;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Mime as FileMime;
use Magento\Framework\Filesystem\Driver\File as Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class AspectRatioApplier
{
    public const REGEXP_PARENT_DIR = '/([^\/]+\/\.\.\/)*?/';
    public const REGEXP_STATIC_VERSION = '/version\d+\//';
    public const REGEXP_ATTR_STYLE = '/style=(\'|")(.*?)(\'|")/';
    public const REGEXP_ATTR_VIEWBOX =
        '/viewBox=[\'"](?<startX>\d+) (?<startY>\d+) (?<width>[\d\.]+) (?<height>[\d\.]+)[\'"]/i';
    public const WIDTH_KEY = 0;
    public const HEIGHT_KEY = 1;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $imagePath = '';

    /**
     * @var string
     */
    private $aspectRatioStyle = '';

    /**
     * @var FileMime
     */
    private $fileMime;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        FileMime $fileMime
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->fileMime = $fileMime;
    }

    /**
     * Inserts aspect-ratio style to make LazyLoad image placeholder dimensions equal to original image, not square
     */
    public function apply(string &$image, string $imgPath): void
    {
        $this->initImagePath($imgPath);

        try {
            if ($this->filesystem->isExists($this->imagePath) && $this->filesystem->isFile($this->imagePath)) {
                $this->prepareAspectRatioStyle();
                $this->insertAspectRatioToStyles($image);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function initImagePath(string $imgPath): void
    {
        $imagePath = str_replace(
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB),
            '',
            $imgPath
        );

        if (strpos($imagePath, DirectoryList::STATIC_VIEW) === 0) {
            $imagePath = preg_replace(self::REGEXP_STATIC_VERSION, '', $imagePath);
        }

        $this->imagePath = (string)preg_replace(self::REGEXP_PARENT_DIR, '', $imagePath);
    }

    private function prepareAspectRatioStyle(): void
    {
        $fileContent = $this->filesystem->fileGetContents($this->imagePath);

        if ($this->isSvg()) {
            preg_match(self::REGEXP_ATTR_VIEWBOX, $fileContent, $viewBox);
            $width = (float)$viewBox['width'];
            $height = (float)$viewBox['height'];
        } else {
            $size = getimagesizefromstring($fileContent);
            $width = (int)$size[self::WIDTH_KEY];
            $height = (int)$size[self::HEIGHT_KEY];
        }

        $this->aspectRatioStyle = 'aspect-ratio: ' . $width . '/' . $height . ';';
    }

    private function isSvg(): bool
    {
        return $this->fileMime->getMimeType($this->imagePath) == 'image/svg+xml';
    }

    private function insertAspectRatioToStyles(string &$image): void
    {
        if (preg_match(self::REGEXP_ATTR_STYLE, $image, $styles)) {
            $image = str_replace(
                $styles[0],
                'style=' . $styles[1] . $this->aspectRatioStyle . $styles[2] . $styles[3],
                $image
            );
        } else {
            $image = preg_replace('#(/)?>$#', ' style="' . $this->aspectRatioStyle . '"$1>', $image);
        }
    }
}
