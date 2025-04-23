<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Ui\Component\Form\Field;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\App\Filesystem\DirectoryList;

class Icon extends Field
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Mime
     */
    private $mime;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Mime $mime,
        Filesystem $filesystem,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->filesystem = $filesystem;
        $this->mime = $mime;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data'][ItemInterface::ICON]) && is_string($dataSource['data'][ItemInterface::ICON])) {
            $filePath = $dataSource['data'][ItemInterface::ICON];
            $dataSource['data'][ItemInterface::ICON] = [$this->getImageData($filePath)];
        }

        return parent::prepareDataSource($dataSource);
    }

    private function getImageData(string $filePath): array
    {
        try {
            if ($this->getPubDirectory()->isExist($filePath)) {
                $stat = $this->getPubDirectory()->stat($filePath);
                $absPath = $this->getPubDirectory()->getAbsolutePath($filePath);
                $fileName = explode('/', $filePath);
                $fileName = array_pop($fileName);

                $imageData = [
                    'name' => $fileName,
                    'url' => $filePath,
                    'size' => $stat['size'] ?? 0,
                    'type' => $this->mime->getMimeType($absPath)
                ];
            }
        } catch (\Exception $exception) {
            $imageData = [];
        }

        return $imageData ?? [];
    }

    /**
     * Get Pub Directory read instance
     *
     * @return ReadInterface
     */
    private function getPubDirectory()
    {
        if (!isset($this->pubDirectory)) {
            $this->pubDirectory = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        }

        return $this->pubDirectory;
    }
}
