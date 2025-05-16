<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\LazyLoad;

use Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\Validation\ValidatorComposite;

class LazyLoadValidatorComposite extends ValidatorComposite
{
    public function getMessages(): array
    {
        $messages = parent::getMessages();

        if (!empty($messages)) {
            $messages = [
                __(
                    'Please make sure that all required fields are fulfilled to connect to SpeedSize '
                    . '(Stores → Configuration → Amasty Extensions → Image Optimizer → SpeedSize Settings).'
                )
            ];
        }

        return $messages;
    }
}
