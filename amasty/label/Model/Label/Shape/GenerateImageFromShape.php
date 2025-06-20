<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Shape;

use Amasty\Label\Model\Label\Parts\FrontendSettings\GetImageFilePath;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File;
use RuntimeException as RuntimeException;

class GenerateImageFromShape
{
    /**
     * @var DataProvider
     */
    private $shapeDataProvider;

    /**
     * @var GetImageFilePath
     */
    private $getImageFilePath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    public function __construct(
        DataProvider $shapeDataProvider,
        ?File $fileProcessor, // @deprecated
        GetImageFilePath $getImageFilePath,
        Filesystem $filesystem = null
    ) {
        $this->shapeDataProvider = $shapeDataProvider;
        $this->getImageFilePath = $getImageFilePath;
        // OM for backward compatibility
        $this->filesystem = $filesystem ?? ObjectManager::getInstance()->get(Filesystem::class);
    }

    public function execute(string $shapeCode, ?string $color = null): string
    {
        $imageFileName = $this->generateImageFileName($shapeCode, $color);
        $filePath = $this->getImageFilePath->execute($imageFileName, false);

        if (!$this->getMediaDirectory()->isExist($filePath)) {
            if (!empty($color)) {
                $shapeContent = $this->shapeDataProvider->getContent($shapeCode);
                $shapeContent = $this->changeShapeColor($shapeCode, $color, $shapeContent);
                $this->getMediaDirectory()->writeFile($filePath, $shapeContent);
            } else {
                $shapePath = $this->shapeDataProvider->getFilePath($shapeCode);
                $this->getMediaDirectory()->copyFile($shapePath, $filePath);
            }
        }

        return $imageFileName;
    }

    /**
     * @param string $shapeCode
     * @param string $newColor
     * @param string $svgContent
     *
     * @return string
     * @throws RuntimeException
     */
    private function changeShapeColor(string $shapeCode, string $newColor, string $svgContent): string
    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;

        if ($document->loadXML($svgContent)) {
            if ($this->shapeDataProvider->isTransparent($shapeCode)) {
                $allTags = $document->getElementsByTagName("g");

                if ($allTags->length == 0) {
                    $allTags = $document->getElementsByTagName("path");
                }

                if ($item = $allTags->item(0)) {
                    $item->setAttribute('stroke', $newColor);
                    $svgContent = $document->saveXML($document);
                }
            } else {
                $allTags = $document->getElementsByTagName("path");

                foreach ($allTags as $tag) {
                    $vectorColor = $tag->getAttribute('fill');

                    if (strtoupper($vectorColor) != '#FFFFFF') {
                        $tag->setAttribute('fill', $newColor);
                        $svgContent = $document->saveXML($document);
                        break;
                    }
                }
            }
        } else {
            throw new RuntimeException(
                __('Failed to load shape %1 as XML.  It probably contains malformed data.', $shapeCode)->render()
            );
        }

        return $svgContent;
    }

    private function generateImageFileName(string $shapeCode, string $color): string
    {
        $colorHex = str_replace('#', '', $color);
        $name = $colorHex ? "{$shapeCode}_{$colorHex}" : $shapeCode;

        return $name . DataProvider::SHAPE_EXTENSION;
    }

    private function getMediaDirectory(): WriteInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
