<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Menu;

use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class GetImagePath
{
    public const AVAILABLE_TYPES = [
        'jpg',
        'jpeg',
        'png'
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    public function __construct(
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader = null
    ) {
        $this->storeManager = $storeManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param array|string|null $imageData
     * @return string|null
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute($imageData): ?string
    {
        return $this->getImagePath($imageData);
    }

    /**
     * @param array|string|null $imageData
     * @return string|null
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImagePath($imageData): ?string
    {
        if ($this->isTmpFileAvailable($imageData) && ($imageName = $this->getUploadedImageName($imageData))) {
            $baseMediaDir = $this->storeManager->getStore()->getBaseMediaDir();
            $newImgRelativePath = $this->imageUploader->moveFileFromTmp($imageName, true);
            $imageData = '/' . $baseMediaDir . '/' . $newImgRelativePath;
        } elseif (is_array($imageData)) {
            $imageData = $imageData[0]['url'] ?? null;
        }

        if ($imageData && !$this->isAvailableType($imageData ?? '')) {
            throw new LocalizedException(__('We don\'t recognize or support the extension type of a file uploaded to
            Menu Icon setting. Please make sure you are using the allowed format: .jpg or .png'));
        }

        return $imageData ?? null;
    }

    private function isTmpFileAvailable($value): bool
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    private function getUploadedImageName($value): string
    {
        return is_array($value) && isset($value[0]['name']) ? $value[0]['name'] : '';
    }

    private function isAvailableType(string $image): bool
    {
        $type = explode('.', $image);

        return in_array(end($type), self::AVAILABLE_TYPES);
    }
}
