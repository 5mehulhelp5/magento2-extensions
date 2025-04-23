<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Shape;

use Amasty\Label\Model\Label\Parts\FrontendSettings\ImagePathFormatter;
use InvalidArgumentException as InvalidArgumentException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\UrlInterface;

class DataProvider
{
    public const SHAPE_EXTENSION = '.svg';

    /**
     * @var array|string[]
     */
    private $transparentShapes = [
        'transparent_circle',
        'transparent_rectangle'
    ];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $shapeTypes;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    public function __construct(
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        ?IoFile $ioFile, // @deprecated
        array $shapeTypes = [],
        array $transparentShapeCodes = []
    ) {
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->shapeTypes = array_merge($this->getDefaultShapes(), $shapeTypes);
        $this->transparentShapes = array_merge($this->transparentShapes, $transparentShapeCodes);
    }

    public function isShapeExists(string $shapeType): bool
    {
        return isset($this->shapeTypes[$shapeType])
            && $this->getMediaDirectory()->isExist($this->getFilesystemPath($shapeType));
    }

    /**
     * @return string[]
     */
    public function getTransparentShapes(): array
    {
        return $this->transparentShapes;
    }

    public function isTransparent(string $shapeCode): bool
    {
        $this->assertShapeExists($shapeCode);

        return in_array($shapeCode, $this->transparentShapes);
    }

    /**
     * @param string $shapeType
     * @return string
     * @throws InvalidArgumentException
     */
    public function getUrl(string $shapeType): string
    {
        $this->assertShapeExists($shapeType);

        return sprintf(
            '%s%s%s%s',
            $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]),
            ImagePathFormatter::AMASTY_LABEL_MEDIA_PATH,
            $shapeType,
            self::SHAPE_EXTENSION
        );
    }

    /**
     * @param string $shapeType
     * @return string
     * @throws InvalidArgumentException
     */
    public function getFilePath(string $shapeType): string
    {
        $this->assertShapeExists($shapeType);

        return $this->getFilesystemPath($shapeType);
    }

    public function getAllTypes(): array
    {
        return $this->shapeTypes;
    }

    public function getContent(string $shapeType): string
    {
        return $this->getMediaDirectory()->readFile($this->getFilePath($shapeType));
    }

    /**
     * @param string $shapeType
     * @throws InvalidArgumentException
     */
    private function assertShapeExists(string $shapeType): void
    {
        if (!$this->isShapeExists($shapeType)) {
            throw new InvalidArgumentException(__('Provided shape doesn\'t exist')->render());
        }
    }

    private function getFilesystemPath(string $shapeType): string
    {
        return $this->getLabelFolder() . DIRECTORY_SEPARATOR . $shapeType . self::SHAPE_EXTENSION;
    }

    private function getLabelFolder(): string
    {
        return $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            ImagePathFormatter::AMASTY_LABEL_MEDIA_PATH
        );
    }

    private function getDefaultShapes(): array
    {
        return  [
            'circle' => __('Circle'),
            'rquarter' => __('Right Quarter'),
            'rbquarter' => __('Right Bottom Quarter'),
            'lquarter' => __('Left Quarter'),
            'lbquarter' => __('Left Bottom Quarter'),
            'list' => __('List'),
            'note' => __('Note'),
            'flag' => __('Flag'),
            'banner' => __('Banner'),
            'tag' => __('Tag'),
            'transparent_rectangle' => __('Transparent Rectangle'),
            'transparent_circle' => __('Transparent Circle'),
            'shape1' => __('Shape 1'),
            'shape2' => __('Shape 2'),
            'shape3' => __('Shape 3'),
            'shape4' => __('Shape 4'),
            'shape5' => __('Shape 5'),
            'shape6' => __('Shape 6'),
            'shape7' => __('Shape 7'),
            'shape8' => __('Shape 8'),
            'shape9' => __('Shape 9'),
            'shape10' => __('Shape 10'),
        ];
    }

    private function getMediaDirectory(): ReadInterface
    {
        if ($this->mediaDirectory === null) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}
