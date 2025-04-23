<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Amasty\SeoRichData\Helper\Config as ConfigHelper;
use Amasty\SeoRichData\Model\ConfigProvider;
use Amasty\SeoRichData\Model\CountryInfo;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;

class Organization implements ProcessorInterface
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CountryInfo
     */
    private $countryInfo;

    public function __construct(
        ConfigHelper $configHelper,
        UrlInterface $urlBuilder,
        ConfigProvider $configProvider,
        CountryInfo $countryInfo = null // TODO move to not optional
    ) {
        $this->configHelper = $configHelper;
        $this->urlBuilder = $urlBuilder;
        $this->configProvider = $configProvider;
        $this->countryInfo = $countryInfo ?? ObjectManager::getInstance()->get(CountryInfo::class);
    }

    public function process(array $data): array
    {
        if (!$this->configHelper->forOrganizationEnabled()) {
            return $data;
        }

        $data['organization'] = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'url' => $this->urlBuilder->getBaseUrl()
        ];

        if ($name = $this->configHelper->getOrganizationName()) {
            $data['organization']['name'] = $name;
        }

        if ($logoUrl = $this->configHelper->getOrganizationLogo()) {
            $data['organization']['logo'] = $logoUrl;
        }

        if ($description = $this->configHelper->getOrganizationDescription()) {
            $data['organization']['description'] = $description;
        }

        foreach ($this->configHelper->getOrganizationContacts() as $contactType => $contact) {
            $data['organization']['contactPoint'][] = [
                "@type" => "ContactPoint",
                "telephone" => $contact,
                "contactType" => str_replace('_', ' ', $contactType)
            ];
        }

        if ($country = $this->countryInfo->getCountryId()) {
            $data['organization']['address']['addressCountry'] = $country;
        }

        if ($postalCode = $this->configHelper->getPostalCode()) {
            $data['organization']['address']['postalCode'] = $postalCode;
        }

        if ($region = $this->configHelper->getOrganizationRegion()) {
            $data['organization']['address']['addressRegion'] = $region;
        }

        if ($city = $this->configHelper->getOrganizationCity()) {
            $data['organization']['address']['addressLocality'] = $city;
        }

        if ($city = $this->configProvider->getStreetAddress()) {
            $data['organization']['address']['streetAddress'] = $city;
        }

        return $data;
    }
}
