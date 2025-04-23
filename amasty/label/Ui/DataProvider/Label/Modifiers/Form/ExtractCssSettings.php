<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Model\LabelRegistry;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class ExtractCssSettings implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    public const TEXT_SIZE_PATTERN = '/(?<=font-size:)[\sa-z\d]+(?=;)/';
    public const COLOR_SETTINGS_PATTERN = '/(^| |;|; )(color:)([\s]{0,}#[\da-fA-F]{3,6})(?=;)/';

    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function __construct(
        LabelRegistry $labelRegistry
    ) {
        $this->labelRegistry = $labelRegistry;
    }

    protected function executeIfLabelExists(int $labelId, array $data): array
    {
        foreach (ConvertCatalogPartsImages::PARTS_PREFIXES as $prefix) {
            $styleKey = "{$prefix}_" . LabelFrontendSettingsInterface::STYLE;

            if (!empty($data[$labelId][$styleKey])) {
                $styles = $data[$labelId][$styleKey];
                preg_match(self::COLOR_SETTINGS_PATTERN, $styles, $result);

                if (isset($result[3])) {
                    $data[$labelId]["{$prefix}_color"] = trim($result[3]);
                }

                preg_match(self::TEXT_SIZE_PATTERN, $styles, $result);

                if (isset($result[0])) {
                    $data[$labelId]["{$prefix}_size"] = trim($result[0]);
                }
            }
        }

        return $data;
    }
}
