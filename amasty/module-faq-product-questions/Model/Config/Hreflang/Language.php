<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config\Hreflang;

use Magento\Framework\Data\OptionSourceInterface;

class Language implements OptionSourceInterface
{
    public const CODE_XDEFAULT = 'x-default';
    public const CURRENT_STORE = '1';

    /**
     * @var string[]
     */
    private $languageTranslation;

    public function __construct(
        array $languageTranslation
    ) {
        $this->languageTranslation = $languageTranslation;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [['value' => self::CURRENT_STORE, 'label' => __('From Current Store Locale')]];
        foreach ($this->languageTranslation as $code => $language) {
            $options[] = ['value' => $code, 'label' => $language . ' (' . $code . ')'];
        }

        return $options;
    }
}
