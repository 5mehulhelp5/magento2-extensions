<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Ui\DataProvider\Label\Modifiers\Form;

use Amasty\Label\Model\LabelRegistry;

trait LabelSpecificSettingsTrait
{
    /**
     * @var LabelRegistry
     */
    private $labelRegistry;

    public function modifyMeta(array $meta): array
    {
        return $meta;
    }

    public function modifyData(array $data): array
    {
        $label = $this->labelRegistry->getCurrentLabel();

        if ($label !== null) {
            $labelId = $label->getLabelId();

            if (isset($data[$labelId])) {
                $data = $this->executeIfLabelExists($labelId, $data);
            }
        }

        return $data;
    }

    abstract protected function executeIfLabelExists(int $labelId, array $data): array;
}
