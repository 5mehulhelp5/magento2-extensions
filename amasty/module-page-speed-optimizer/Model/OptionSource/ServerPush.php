<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager;

class ServerPush implements OptionSourceInterface
{
    public const NO = 0;
    public const BASIC = 1;
    public const ADVANCED = 2;

    /**
     * @var array [key, label, requested_module]
     */
    private $options;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Manager $moduleManager,
        array $options = []
    ) {
        $this->options = $options;
        $this->moduleManager = $moduleManager;
    }

    public function toOptionArray(): array
    {
        $optionArray = [];
        foreach ($this->options as $optionData) {
            if (!isset($optionData['key'], $optionData['label'])) {
                continue;
            }
            $value = $optionData['key'];
            $label = $optionData['label'];
            $disabled = false;

            if (($optionData['requested_module'] ?? '')
                && !$this->moduleManager->isEnabled($optionData['requested_module'])
            ) {
                $label .= ' ' . __('(Subscribe to Unlock)');
                $disabled = true;
            }
            $optionArray[] = ['value' => $value, 'label' => $label, 'disabled' => $disabled];
        }
        $optionArray[] = ['value' => self::NO, 'label' => __('No')]; //adding "no" option last

        return $optionArray;
    }
}
