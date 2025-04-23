<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\Backend\Ui;

use Magento\Framework\Stdlib\ArrayManager;

class HideField
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    public function execute(array $meta, string $fieldSet, string $field): array
    {
        $path = sprintf(
            '%s/%s/%s/%s',
            $fieldSet,
            'children',
            $field,
            'arguments/data/config/visible'
        );
        $meta = $this->arrayManager->set($path, $meta, false);

        $path = sprintf(
            '%s/%s/%s/%s',
            $fieldSet,
            'children',
            $field,
            'arguments/data/config/hidden'
        );
        $meta = $this->arrayManager->set($path, $meta, true);

        return $meta;
    }
}
