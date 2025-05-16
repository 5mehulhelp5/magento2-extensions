<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amoptimizer/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const MULTIPROCESS_ENABLED = 'images/multiprocess_enabled';
    public const MAX_JOBS_COUNT = 'images/process_count';
    public const OPTIMIZE_AUTOMATICALLY = 'images/optimize_automatically';
    public const OPTIMIZE_TYPE = 'images/optimization_type';
    public const IMAGES_PER_REQUEST = 'images/process_images_per_request';
    public const IMAGES_RESOLUTIONS_LIB = 'images/resolutions_lib';
    public const ENABLED_PRODUCT_IMAGES_ONLY = 'images/optimize_enabled_products';
    public const WEBP_COMPRESSION_QUALITY = 'images/webp_compression_quality';
    public const JPEG_COMMAND = 'images/jpeg_tool';
    public const PNG_COMMAND = 'images/png_tool';
    public const GIF_COMMAND = 'images/gif_tool';
    public const DUMP_ORIGINAL = 'images/dump_original';
    public const IGNORE_IMAGES = 'images/ignore_list';
    public const RESOLUTIONS = 'images/resolutions';
    public const RESIZE_ALGORITHM = 'images/resize_algorithm';
    public const WEBP_COMMAND = 'images/webp';

    public const REPLACE_IMAGES_USING_USER_AGENT = 'images/replace_images_using_user_agent';
    public const REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST = 'images/replace_images_using_user_agent_ignore_list';
    public const REPLACE_STRATEGY = 'replace_images_general/replace_strategy';
    public const REPLACE_IGNORE_IMAGES = 'replace_images_general/replace_ignore';
    public const SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES = 'replace_images_general/support_third_party_image_attributes';

    public const PART_REPLACE_STRATEGY = '/replace_strategy';
    public const PART_REPLACE_IGNORE = '/replace_ignore';
    public const PART_ENABLE_CUSTOM_REPLACE = '/enable_custom_replace';
    public const PART_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES = '/support_third_party_image_attributes';
    /**#@-*/

    public function isMultiprocessEnabled()
    {
        return $this->isSetFlag(self::MULTIPROCESS_ENABLED);
    }

    public function getMaxJobsCount()
    {
        if (!function_exists('pcntl_fork')) {
            return 1;
        }
        $maxJobs = (int)$this->getValue(self::MAX_JOBS_COUNT) > 1
            ? (int)$this->getValue(self::MAX_JOBS_COUNT)
            : 1;

        return $this->isMultiprocessEnabled() ? $maxJobs : 1;
    }

    public function isAutomaticallyOptimizeImages(): bool
    {
        return $this->isSetFlag(self::OPTIMIZE_AUTOMATICALLY);
    }

    public function isOptimizeEnabledProductImages(): bool
    {
        return $this->isSetFlag(self::ENABLED_PRODUCT_IMAGES_ONLY);
    }

    public function getJpegCommand(): string
    {
        return (string)$this->getValue(self::JPEG_COMMAND);
    }

    public function getPngCommand(): string
    {
        return (string)$this->getValue(self::PNG_COMMAND);
    }

    public function getWebpCommand(): string
    {
        return (string)$this->getValue(self::WEBP_COMMAND);
    }

    public function getGifCommand(): string
    {
        return (string)$this->getValue(self::GIF_COMMAND);
    }

    public function getImagesPerRequest(): int
    {
        return (int)$this->getValue(self::IMAGES_PER_REQUEST);
    }

    public function getWebpCompressionQuality(): int
    {
        return (int)$this->getValue(self::WEBP_COMPRESSION_QUALITY);
    }

    public function isDumpOriginal(): bool
    {
        return $this->isSetFlag(self::DUMP_ORIGINAL);
    }

    public function getResolutions(): array
    {
        return explode(',', (string)$this->getValue(self::RESOLUTIONS));
    }

    public function getConfig($path)
    {
        return $this->getValue($path);
    }

    public function getCustomValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function getResizeAlgorithm()
    {
        return (int)$this->getValue(self::RESIZE_ALGORITHM);
    }

    public function getResolutionsLib(): string
    {
        return (string)$this->getValue(self::IMAGES_RESOLUTIONS_LIB);
    }

    public function isReplaceImagesUsingUserAgent(): bool
    {
        return $this->isSetFlag(self::REPLACE_IMAGES_USING_USER_AGENT);
    }

    public function getReplaceImagesUsingUserAgentIgnoreList(): array
    {
        return $this->convertStringToArray($this->getValue(self::REPLACE_IMAGES_USING_USER_AGENT_IGNORE_LIST));
    }

    public function getImageReplaceStrategy(): int
    {
        return (int)$this->getValue(self::REPLACE_STRATEGY);
    }

    public function getReplaceIgnoreList(): array
    {
        return $this->convertStringToArray($this->getValue(self::REPLACE_IGNORE_IMAGES));
    }

    public function getSupportThirdPartyImageAttributes(): array
    {
        return $this->convertStringToArray(
            str_replace("\r\n", PHP_EOL, (string)$this->getValue(self::SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES))
        );
    }

    public function convertStringToArray(?string $data, string $separator = PHP_EOL): array
    {
        return (!empty($data)) ? array_filter(array_map('trim', explode($separator, (string)$data))) : [];
    }
}
