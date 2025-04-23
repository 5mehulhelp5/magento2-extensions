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

class SelectTypeTab implements ModifierInterface
{
    use LabelSpecificSettingsTrait;

    public const TEXT_ONLY = 0;
    public const IMAGE = 2;
    public const SHAPE = 1;

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
        foreach (ConvertCatalogPartsImages::PARTS_PREFIXES as $partPrefix) {
            $typeKey = "{$partPrefix}_label_type";
            $imageKey = "{$partPrefix}_" . LabelFrontendSettingsInterface::IMAGE;
            $data[$labelId][$typeKey] = empty($data[$labelId][$imageKey]) ? self::TEXT_ONLY : self::IMAGE;
        }

        return $data;
    }
}
