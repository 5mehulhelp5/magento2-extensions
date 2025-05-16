<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Areas for Magento 2 (System)
 */

namespace Amasty\ShippingArea\Api\Data;

interface AreaInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const AREA_ID = 'area_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IS_ENABLED = 'is_enabled';
    public const COUNTRY_CONDITION = 'country_condition';
    public const COUNTRY_SET = 'country_set';
    public const STATE_CONDITION = 'state_condition';
    public const STATE_SET_LISTING = 'state_set_listing';
    public const STATE_SET = 'state_set';
    public const CITY_CONDITION = 'city_condition';
    public const CITY_SET = 'city_set';
    public const POSTCODE_CONDITION = 'postcode_condition';
    public const POSTCODE_SET = 'postcode_set';
    public const ADDRESS_CONDITION = 'address_condition';
    public const ADDRESS_SET = 'address_set';
    /**#@-*/

    /**
     * Key for data persistor
     */
    public const FORM_NAMESPACE = 'amasty_shiparea_form';

    /**
     * @return int
     */
    public function getAreaId();

    /**
     * @param int $areaId
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setAreaId($areaId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string|null $description
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getIsEnabled();

    /**
     * @param int $isEnabled
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setIsEnabled($isEnabled);

    /**
     * @return int
     */
    public function getCountryCondition();

    /**
     * @param int $countryCondition
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setCountryCondition($countryCondition);

    /**
     * @return array|null
     */
    public function getCountrySet();

    /**
     * @param array|string|null $countrySet
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setCountrySet($countrySet);

    /**
     * @return int
     */
    public function getStateCondition();

    /**
     * @param int $stateCondition
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setStateCondition($stateCondition);

    /**
     * @return array|string|null
     */
    public function getStateSetListing();

    /**
     * @param array|string|null $stateSetListing
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setStateSetListing($stateSetListing);

    /**
     * @return string|null
     */
    public function getStateSet();

    /**
     * @param string|null $stateSet
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setStateSet($stateSet);

    /**
     * @return int
     */
    public function getCityCondition();

    /**
     * @param int $cityCondition
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setCityCondition($cityCondition);

    /**
     * @return string|null
     */
    public function getCitySet();

    /**
     * @param string|null $citySet
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setCitySet($citySet);

    /**
     * @return int
     */
    public function getPostcodeCondition();

    /**
     * @param int $cityCondition
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setPostcodeCondition($cityCondition);

    /**
     * @return array|null
     */
    public function getPostcodeSet();

    /**
     * @param array|string|null $postcodeSet
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setPostcodeSet($postcodeSet);

    /**
     * @return int
     */
    public function getAddressCondition();

    /**
     * @param int $addressCondition
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setAddressCondition($addressCondition);

    /**
     * @return string|null
     */
    public function getAddressSet();

    /**
     * @param string|null $addressSet
     *
     * @return \Amasty\ShippingArea\Api\Data\AreaInterface
     */
    public function setAddressSet($addressSet);
}
