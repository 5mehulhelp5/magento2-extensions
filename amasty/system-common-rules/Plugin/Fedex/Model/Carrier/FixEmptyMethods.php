<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Plugin\Fedex\Model\Carrier;

use Magento\Fedex\Model\Carrier;

/**
 * Fix Fedex fatal when no available methods on php8.1
 *
 * @see \Magento\Fedex\Model\Carrier::getAllowedMethods second argument of expode should be string
 */
class FixEmptyMethods
{
    /**
     * @param Carrier $subject
     * @param string|false|null $result
     * @param string $field
     * @return string|false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfigData(Carrier $subject, $result, $field)
    {
        if ($field === 'allowed_methods' && $result === null) {
            return '';
        }

        return $result;
    }
}
