<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\Image;

use Amasty\ImageOptimizerUi\Block\Adminhtml\Buttons\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndOptimize extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        return [
            'label' => __('Save and Optimize'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'amimageoptimizer_image_form.amimageoptimizer_image_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    ['save_and_optimize' => 1],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            'on_click' => '',
            'sort_order' => 10
        ];
    }
}
