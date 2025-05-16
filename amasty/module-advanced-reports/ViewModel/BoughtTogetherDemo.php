<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class BoughtTogetherDemo implements ArgumentInterface
{
    public function getSubscribeUrl(): string
    {
        return 'https://amasty.com/docs/doku.php' .
            '?id=magento_2:advanced_reports#additional_packages_provided_in_composer_suggestions';
    }

    public function getIdValue(): string
    {
        return 'magento_2:advanced_reports#additional_packages_provided_in_composer_suggestions';
    }
}
