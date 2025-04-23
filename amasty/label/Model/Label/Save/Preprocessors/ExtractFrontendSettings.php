<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save\Preprocessors;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Model\Label\Parts\MetaProvider;
use Amasty\Label\Model\Label\Save\DataPreprocessorInterface;
use Amasty\Label\Model\ResourceModel\Label\Collection as LabelCollection;
use Amasty\Label\Model\ResourceModel\Label\Grid\Collection as FlatCollection;

class ExtractFrontendSettings implements DataPreprocessorInterface
{
    public function process(array $data): array
    {
        $config = [
            FlatCollection::PRODUCT_PREFIX => LabelCollection::MODE_PDP,
            FlatCollection::CATEGORY_PREFIX => LabelCollection::MODE_LIST,
        ];

        foreach ($config as $partPrefix => $frontendPartType) {
            foreach ($this->getPartKeys($partPrefix, $data) as $frontendSettingKey) {
                $clearKey = str_replace("{$partPrefix}_", '', $frontendSettingKey);
                $data['extension_attributes'][MetaProvider::FRONTEND_SETTINGS_PART]
                     [$frontendPartType][$clearKey] = $data[$frontendSettingKey];
                unset($data[$frontendSettingKey]);
            }
            $data['extension_attributes'][MetaProvider::FRONTEND_SETTINGS_PART][$frontendPartType]
                [LabelFrontendSettingsInterface::TYPE] = $frontendPartType;
        }

        return $data;
    }

    private function getPartKeys(string $keyPrefix, array $data): array
    {
        return array_reduce(array_keys($data), function (array $carry, string $key) use ($keyPrefix): array {
            if (strpos($key, "{$keyPrefix}_") === 0) {
                $carry[] = $key;
            }

            return $carry;
        }, []);
    }
}
