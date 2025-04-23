<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save\Preprocessors;

use Amasty\Label\Api\Data\LabelFrontendSettingsInterface;
use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Save\DataPreprocessorInterface;
use Amasty\Label\Ui\DataProvider\Label\Modifiers\Form\ConvertCatalogPartsImages;
use Magento\Framework\Escaper;

class EscapeLabelsText implements DataPreprocessorInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
    }

    public function process(array $data): array
    {
        $data[LabelInterface::NAME] = $this->escaper->escapeHtml($data[LabelInterface::NAME] ?? '');

        foreach (ConvertCatalogPartsImages::PARTS_PREFIXES as $partPrefix) {
            $textKey = sprintf('%s_%s', $partPrefix, LabelFrontendSettingsInterface::LABEL_TEXT);

            if (!empty($data[$textKey])) {
                $data[$textKey] = $this->escaper->escapeHtml($data[$textKey]);
            }
        }

        return $data;
    }
}
