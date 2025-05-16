<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Framework\File;

use Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\ProcessorsProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;

class UploaderPlugin
{
    public const ALLOWED_IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/gif',
        'image/png',
    ];

    /**
     * @var ProcessorsProvider
     */
    private $processorsProvider;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    public function __construct(
        ProcessorsProvider $processorsProvider,
        Filesystem $filesystem
    ) {
        $this->processorsProvider = $processorsProvider;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * @param Uploader $subject
     * @param bool|array $result
     * @return mixed
     */
    public function afterSave(Uploader $subject, $result)
    {
        if (!isset($result['path']) || !$this->isFilePathAllowed($result['path'])) {
            return $result;
        }

        if ($this->isImageMimeTypeAllowed($result['type'] ?? '')) {
            foreach ($this->processorsProvider->getAll() as $processor) {
                $processor->execute($result['path'] . DIRECTORY_SEPARATOR . $result['file']);
            }
        }

        return $result;
    }

    private function isFilePathAllowed(string $filePath): bool
    {
        $mediaDir = $this->mediaDirectory->getAbsolutePath();

        return strpos($filePath, $mediaDir) !== false;
    }

    private function isImageMimeTypeAllowed(string $mimeType): bool
    {
        return in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES);
    }
}
