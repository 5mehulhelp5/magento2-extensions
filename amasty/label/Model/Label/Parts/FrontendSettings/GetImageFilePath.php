<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Parts\FrontendSettings;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;

class GetImageFilePath
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ImagePathFormatter
     */
    private $imagePathFormatter;

    public function __construct(
        Filesystem $filesystem,
        ?File $driver, // @deprecated
        ImagePathFormatter $imagePathFormatter
    ) {
        $this->filesystem = $filesystem;
        $this->imagePathFormatter = $imagePathFormatter;
    }

    public function execute(?string $imageName, $checkExists = true): ?string
    {
        $mediaFolderPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $imagePath = $this->imagePathFormatter->execute($imageName);
        $labelImagePath = sprintf('%s%s', $mediaFolderPath, $imagePath);

        if ($checkExists) {
            $driver = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getDriver();

            return $driver->isExists($labelImagePath) ? $labelImagePath : null;
        } else {
            return $labelImagePath;
        }
    }
}
