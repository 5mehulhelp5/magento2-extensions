<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model;

use Magento\Directory\Model\CountryFactory;

class CountryInfo
{
    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        CountryFactory $countryFactory,
        ConfigProvider $configProvider
    ) {
        $this->countryFactory = $countryFactory;
        $this->configProvider = $configProvider;
    }

    public function getCountryId(): string
    {
        $countryCode = $this->configProvider->getOrganizationCountry();

        if (!$countryCode) {
            return '';
        }

        return $this->countryFactory->create()->loadByCode($countryCode)->getId();
    }
}
