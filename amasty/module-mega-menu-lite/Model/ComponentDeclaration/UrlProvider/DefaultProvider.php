<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration\UrlProvider;

use Amasty\MegaMenuLite\Api\Component\UrlProviderInterface;
use Magento\Customer\Model\Url as CustomerUrlModel;
use Magento\Framework\UrlInterface;

class DefaultProvider implements UrlProviderInterface
{
    public const LOGIN_URL = 'getLoginUrl';
    public const REGISTER_URL = 'getRegisterUrl';
    public const ACCOUNT_URL = 'getAccountUrl';
    public const WISHLIST_URL = 'getWishlistUrl';
    public const LOGOUT_URL = 'getLogoutUrl';

    /**
     * @var CustomerUrlModel
     */
    private $customerUrlModel;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        CustomerUrlModel $customerUrlModel,
        UrlInterface $urlBuilder
    ) {
        $this->customerUrlModel = $customerUrlModel;
        $this->urlBuilder = $urlBuilder;
    }

    public function getUrl(string $urlPattern): ?string
    {
        return method_exists($this, $urlPattern) ? $this->$urlPattern() : $this->urlBuilder->getUrl($urlPattern);
    }

    public function getLoginUrl(): string
    {
        return $this->customerUrlModel->getLoginUrl();
    }

    public function getRegisterUrl(): string
    {
        return $this->customerUrlModel->getRegisterUrl();
    }

    public function getAccountUrl(): string
    {
        return $this->customerUrlModel->getAccountUrl();
    }

    public function getLogoutUrl(): string
    {
        return $this->customerUrlModel->getLogoutUrl();
    }
}
