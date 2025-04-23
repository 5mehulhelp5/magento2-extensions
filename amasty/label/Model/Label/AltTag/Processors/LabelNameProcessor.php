<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\AltTag\Processors;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Exceptions\TextRenderException;
use Amasty\Label\Model\Label\Text\ProcessorInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class LabelNameProcessor implements ProcessorInterface
{
    public const LABEL_NAME = 'label_name';

    public function getAcceptableVariables(): array
    {
        return [
            self::LABEL_NAME
        ];
    }

    public function getVariableValue(string $variable, LabelInterface $label, ProductInterface $product): string
    {
        switch ($variable) {
            case self::LABEL_NAME:
                return $label->getName();
            default:
                throw new TextRenderException(__('The passed variable %1 could not be processed', $variable));
        }
    }
}
