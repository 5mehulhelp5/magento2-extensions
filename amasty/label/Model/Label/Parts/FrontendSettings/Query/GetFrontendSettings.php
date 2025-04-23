<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Parts\FrontendSettings\Query;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Api\Data\LabelFrontendSettingsInterfaceFactory;
use Amasty\Label\Model\ResourceModel\Label\FrontendSettings\LoadFrontendSettings;
use Magento\Framework\Api\DataObjectHelper;

class GetFrontendSettings
{
    /**
     * @var LoadFrontendSettings
     */
    private $loadFrontendSettings;

    /**
     * @var LabelFrontendSettingsInterfaceFactory
     */
    private $labelFrontendSettingsInterfaceFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array [labelId => [mode => [LabelFrontendSettingsInterface, ...], ...], ...]
     */
    private $cache = [];

    public function __construct(
        LoadFrontendSettings $loadFrontendSettings,
        LabelFrontendSettingsInterfaceFactory $labelFrontendSettingsInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->loadFrontendSettings = $loadFrontendSettings;
        $this->labelFrontendSettingsInterfaceFactory = $labelFrontendSettingsInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @return array [labelId => LabelFrontendSettingsInterface[], ...]
     */
    public function execute(array $labelIds, ?int $mode = null): array
    {
        $labelIdsToLoad = [];
        foreach ($labelIds as $labelId) {
            if (!isset($this->cache[$labelId][$mode])) {
                $labelIdsToLoad[] = $labelId;
            }
        }
        if (empty($labelIdsToLoad)) {
            return $this->loadFromCache($labelIds, $mode);
        }

        $frontendSettings = $this->loadFrontendSettings->execute($labelIdsToLoad, $mode);
        foreach ($frontendSettings as $frontendSetting) {
            $labelId = $frontendSetting[LabelFrontendSettingsInterface::LABEL_ID] ?? null;
            if ($labelId === null) {
                continue;
            }

            /** @var LabelFrontendSettingsInterface $labelFrontendSettings */
            $labelFrontendSettings = $this->labelFrontendSettingsInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $labelFrontendSettings,
                $frontendSetting,
                LabelFrontendSettingsInterface::class
            );
            $this->cache[$labelId][$mode][] = $labelFrontendSettings;
        }

        return $this->loadFromCache($labelIds, $mode);
    }

    /**
     * @return array [labelId => LabelFrontendSettingsInterface[], ...]
     */
    private function loadFromCache(array $labelIds, ?int $mode): array
    {
        $result = [];
        foreach ($labelIds as $labelId) {
            if (!isset($this->cache[$labelId][$mode])) {
                $this->cache[$labelId][$mode][] = $this->labelFrontendSettingsInterfaceFactory->create();
            }
            $result[$labelId] = $this->cache[$labelId][$mode];
        }

        return $result;
    }
}
