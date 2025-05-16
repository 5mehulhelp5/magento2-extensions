<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Output\ReplaceConfig;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Output\PageType\GetConfigPathByPageType;
use Magento\Framework\DataObject;

class ReplaceConfig extends DataObject
{
    public const IS_REPLACE_WITH_USER_AGENT = 'is_replace_with_user_agent';
    public const REPLACE_STRATEGY = 'replace_strategy';
    public const REPLACE_IMAGES_IGNORE_LIST = 'replace_images_ignore_list';
    public const SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES = 'support_third_party_image_attributes';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetConfigPathByPageType
     */
    private $getConfigPathByPageType;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var bool
     */
    private $isEnabledCustomReplace;

    public function __construct(
        ConfigProvider $configProvider,
        GetConfigPathByPageType $getConfigPathByPageType,
        array $data = []
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->getConfigPathByPageType = $getConfigPathByPageType;
        $this->initialize();
    }

    private function initialize()
    {
        $this->configPath = $this->getConfigPathByPageType->execute();
        $this->isEnabledCustomReplace = (bool)$this->configProvider->getConfig(
            $this->configPath . ConfigProvider::PART_ENABLE_CUSTOM_REPLACE
        );

        $this->initIsReplaceWithUserAgent();
        $this->initReplaceImagesStrategy();
        $this->initReplaceImagesIgnoreList();
        $this->initSupportThirdPartyImageAttributes();
    }

    private function initIsReplaceWithUserAgent(): void
    {
        $this->setData(
            self::IS_REPLACE_WITH_USER_AGENT,
            $this->configProvider->isReplaceImagesUsingUserAgent()
        );
    }

    private function initReplaceImagesStrategy(): void
    {
        if ($this->isEnabledCustomReplace) {
            $value = (int)$this->configProvider->getConfig(
                $this->configPath . ConfigProvider::PART_REPLACE_STRATEGY
            );
        } else {
            $value = $this->configProvider->getImageReplaceStrategy();
        }

        $this->setData(self::REPLACE_STRATEGY, $value);
    }

    private function initReplaceImagesIgnoreList(): void
    {
        $userAgentIgnoreList = [];

        if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
            $userAgentIgnoreList = $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList();
        }

        $replaceImagesIgnoreList = ($this->isEnabledCustomReplace)
            ? $this->configProvider->convertStringToArray(
                $this->configProvider->getConfig($this->configPath . ConfigProvider::PART_REPLACE_IGNORE)
            )
            : $this->configProvider->getReplaceIgnoreList();

        $this->setData(self::REPLACE_IMAGES_IGNORE_LIST, array_merge($replaceImagesIgnoreList, $userAgentIgnoreList));
    }

    private function initSupportThirdPartyImageAttributes(): void
    {
        if ($this->isEnabledCustomReplace) {
            $value = (string)$this->configProvider->getConfig(
                $this->configPath . ConfigProvider::PART_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES
            );
            $value = $this->configProvider->convertStringToArray(str_replace("\r\n", PHP_EOL, $value));
        } else {
            $value = $this->configProvider->getSupportThirdPartyImageAttributes();
        }

        $this->setData(self::SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES, $value);
    }
}
