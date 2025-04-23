<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Patch\Data;

use Amasty\Base\Helper\Deploy;
use Amasty\Base\Model\FilesystemProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validation\ValidationException;

/**
 * Replace some of label shapes because they have been changed.
 */
class DeployPubFolderV2 implements DataPatchInterface
{
    public const STATIC_FILES_FOLDER = 'data/pub';

    private const REPLACED_SHAPES = [
        'shape2.svg',
        'shape6.svg',
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    private $rootWrite;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    private $rootRead;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ComponentRegistrar $componentRegistrar,
        FilesystemProvider $filesystemProvider
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->componentRegistrar = $componentRegistrar;
        $this->filesystem = $filesystemProvider->get();
        $this->rootWrite = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->rootRead = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    public static function getDependencies(): array
    {
        return [
            DeployPubFolder::class
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $this->replaceLabelShape();

        return $this;
    }

    private function replaceLabelShape(): void
    {
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Amasty_Label');

        try {
            $this->deployFolder($moduleDir . DIRECTORY_SEPARATOR . self::STATIC_FILES_FOLDER);
        } catch (ValidationException $e) {
            null; //skip this step
        }
    }

    private function deployFolder($folder): void
    {
        $fromPath = $this->rootRead->getRelativePath($folder);
        //phpcs:ignore
        $baseName = basename($fromPath);

        foreach ($this->getFilesToReplace($fromPath) as $file) {
            $newFileName = $this->getNewFilePath(
                $file,
                $fromPath,
                ltrim('/' . $baseName, '/')
            );

            if ($this->rootRead->isFile($file)) {
                $this->rootWrite->copyFile($file, $newFileName);

                $this->rootWrite->changePermissions(
                    $newFileName,
                    Deploy::DEFAULT_FILE_PERMISSIONS
                );
            }
        }
    }

    private function getFilesToReplace($fromPath): array
    {
        $files = $this->rootRead->readRecursively($fromPath);
        return array_filter($files, function ($file) {
            //phpcs:ignore
            return in_array(basename($file), self::REPLACED_SHAPES);
        });
    }

    private function getNewFilePath($filePath, $fromPath, $toPath): string
    {
        return str_replace($fromPath, $toPath, $filePath);
    }
}
