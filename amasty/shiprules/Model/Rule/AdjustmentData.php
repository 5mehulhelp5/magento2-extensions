<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Rules for Magento 2
 */

namespace Amasty\Shiprules\Model\Rule;

/**
 * Adjustment Data Model
 */
class AdjustmentData
{
    public const MIN = 'minimal_value';
    public const MAX = 'maximal_value';

    /**
     * @var float
     */
    private $value = 0;

    /**
     * @var string
     */
    private $rateKey = '';

    /**
     * @var array
     */
    private $rateTotalValue = [
        self::MIN => null,
        self::MAX => null,
    ];

    /**
     * @param float $minValue
     * @param float $maxValue
     *
     * @return $this
     */
    public function setRateTotal($minValue, $maxValue)
    {
        $this->rateTotalValue = [
            self::MIN => $this->rateTotalValue[self::MIN] !== null
                ? min($this->rateTotalValue[self::MIN], $minValue) : $minValue,
            self::MAX => $this->rateTotalValue[self::MAX] !== null
                ? max($this->rateTotalValue[self::MAX], $maxValue) : $maxValue,
        ];

        return $this;
    }

    /**
     * @param string $rateKey
     *
     * @return AdjustmentData
     */
    public function setRateKey($rateKey)
    {
        $this->rateKey = $rateKey;

        return $this;
    }

    /**
     * @param float $value
     *
     * @return AdjustmentData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRateKey()
    {
        return $this->rateKey;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getRateTotalRange()
    {
        return $this->rateTotalValue;
    }

    public function _resetState(): void
    {
        $this->rateTotalValue = [
            self::MIN => null,
            self::MAX => null,
        ];
        $this->value = 0;
        $this->rateKey = '';
    }
}
