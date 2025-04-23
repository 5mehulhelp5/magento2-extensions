<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Api\Label\GetLabelImageUrlInterface;
use Amasty\Label\Model\Label\Parts\FrontendSettings\GetImageFilePath;
use Amasty\Label\Model\LabelRegistry;
use Amasty\Label\Model\ResourceModel\Label\Grid\Collection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class ConvertCatalogPartsImages implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    public const PARTS_PREFIXES = [Collection::PRODUCT_PREFIX, Collection::CATEGORY_PREFIX];

    /**
     * @var GetImageFilePath
     */
    private $getImageFilePath;

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    /**
     * @var GetLabelImageUrlInterface
     */
    private $getLabelImageUrl;

    /**
     * @var Mime
     */
    private $mimeInfoRetriever;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Mime $mimeInfoRetriever,
        GetImageFilePath $getImageFilePath,
        LabelRegistry $labelRegistry,
        GetLabelImageUrlInterface $getLabelImageUrl,
        ?File $driver, // @deprecated
        Filesystem $filesystem = null
    ) {
        $this->getImageFilePath = $getImageFilePath;
        $this->labelRegistry = $labelRegistry;
        $this->getLabelImageUrl = $getLabelImageUrl;
        $this->mimeInfoRetriever = $mimeInfoRetriever;
        // OM for backward compatibility
        $this->filesystem = $filesystem ?? ObjectManager::getInstance()->get(Filesystem::class);
    }

    protected function executeIfLabelExists(int $labelId, array $data): array
    {
        foreach (self::PARTS_PREFIXES as $partsName) {
            $imageKey = $partsName . '_' . LabelFrontendSettingsInterface::IMAGE;

            if (!empty($data[$labelId][$imageKey])) {
                $imageName = $data[$labelId][$imageKey];
                unset($data[$labelId][$imageKey]);
                $imageMeta = $this->getImageMetaInfo($imageName);

                if ($imageMeta !== null) {
                    $data[$labelId][$imageKey][] = $imageMeta;
                }
            }
        }

        return $data;
    }

    private function getImageMetaInfo(string $imageName): ?array
    {
        $result = null;
        $imagePath = $this->getImageFilePath->execute($imageName);

        if ($imagePath !== null) {
            $driver = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getDriver();
            $fileStats = $driver->stat($imagePath);

            $result = [
                'name' => $imageName,
                'url' => $this->getLabelImageUrl->execute($imageName),
                'type' => $this->mimeInfoRetriever->getMimeType($imagePath),
                'size' => $fileStats['size'] ?? 0
            ];
        }

        return $result;
    }
}
