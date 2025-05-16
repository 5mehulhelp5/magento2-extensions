<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config\Hreflang;

use Magento\Directory\Model\Config\Source\Country\Full as CountrySource;
use Magento\Framework\Data\OptionSourceInterface;

class Country implements OptionSourceInterface
{
    public const DONT_ADD = '0';
    public const CURRENT_STORE = '1';

    /**
     * @var CountrySource
     */
    private $countrySource;

    public function __construct(CountrySource $countrySource)
    {
        $this->countrySource = $countrySource;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => self::DONT_ADD, 'label' => __("Don't Add")],
            ['value' => self::CURRENT_STORE, 'label' => __('From Current Store Default Country')]
        ];

        $countries = array_map(
            function ($row) {
                $row['label'] .= ' (' . $row['value'] . ')';

                return $row;
            },
            $this->countrySource->toOptionArray()
        );

        return $options + $countries;
    }
}
