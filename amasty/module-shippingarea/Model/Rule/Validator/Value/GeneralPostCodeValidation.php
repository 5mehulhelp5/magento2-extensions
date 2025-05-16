<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Model\Rule\Validator\Value;

use Amasty\ShippingArea\Model\Area;
use Amasty\ShippingArea\Model\System\ConditionOptionProvider;

class GeneralPostCodeValidation
{
    /**
     * @param Area $area
     * @param string $postcode
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validatePostcode(Area $area, string $postcode): bool
    {
        $result = true;

        if ($area->getPostcodeCondition()) {
            $result = $this->comparePostcode($area, $postcode);

            if ((int)$area->getPostcodeCondition() === ConditionOptionProvider::CONDITION_EXCLUDE) {
                $result = !$result;
            }
        }

        return $result;
    }

    /**
     * @param Area $area
     * @param string $postcode
     * @return bool
     */
    public function comparePostcode(Area $area, string $postcode): bool
    {
        $postcodeSet = $area->getPostcodeSet();
        $postcodeData = $this->extractDataFromZip($postcode);

        if (is_array($postcodeSet)) {
            foreach ($postcodeSet as $zipRow) {

                if (empty($zipRow['zip_to'])) {
                    if (strcasecmp($postcode, $zipRow['zip_from']) === 0) {
                        return true;
                    }

                    continue;
                }
                $zipFrom = $this->extractDataFromZip($zipRow['zip_from']);
                $zipTo = $this->extractDataFromZip($zipRow['zip_to']);

                if ($zipFrom['area'] && (strcasecmp($postcodeData['area'], $zipFrom['area']) !== 0)) {
                    continue;
                }

                if (($zipFrom['district'] <= $postcodeData['district'])
                    && ($zipTo['district'] >= $postcodeData['district'])
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $zip
     * @return array('area' => string, 'district' => int)
     */
    private function extractDataFromZip(string $zip): array
    {
        $dataZip = ['area' => '', 'district' => ''];

        if (!empty($zip)) {
            $zipSpell = str_split($zip);
            
            foreach ($zipSpell as $element) {
                if ($element === ' ') {
                    break;
                }

                if (is_numeric($element)) {
                    $dataZip['district'] = $dataZip['district'] . $element;
                } elseif (empty($dataZip['district'])) {
                    $dataZip['area'] = $dataZip['area'] . $element;
                }
            }
        }

        return $dataZip;
    }
}
