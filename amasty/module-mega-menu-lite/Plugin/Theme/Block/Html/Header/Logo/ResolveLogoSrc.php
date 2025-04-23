<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\Theme\Block\Html\Header\Logo;

use Amasty\Base\Model\MagentoVersion;
use Amasty\MegaMenuLite\Model\ConfigProvider;
use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Theme\Block\Html\Header\Logo as LogoBlock;

/**
 * @see \Magento\Theme\Block\Html\Header\Logo::getLogoSrc
 */
class ResolveLogoSrc extends LogoBlock
{
    public const VERSION_2_4_3 = '2.4.3';

    public const DEFAULT_LOGO = 'images/logo.svg';

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        MagentoVersion $magentoVersion,
        ConfigProvider $configProvider,
        UrlInterface $urlBuilder,
        Context $context,
        Database $fileStorageHelper
    ) {
        $this->magentoVersion = $magentoVersion;
        $this->configProvider = $configProvider;
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $fileStorageHelper);
    }

    public function afterGetLogoSrc(LogoBlock $subject, string $path): ?string
    {
        if (version_compare($this->magentoVersion->get(), self::VERSION_2_4_3, '>=')) {
            $storeLogoPath = $this->configProvider->getHeaderLogoSrc();

            $path = Logo::UPLOAD_DIR . '/' . $storeLogoPath;
            $logoUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $path;

            if ($path !== null && $this->_isFile($path)) {
                $path = $logoUrl;
            } elseif ($this->getLogoFile()) {
                $path = $this->getViewFileUrl($this->getLogoFile());
            } else {
                $path = $this->getViewFileUrl(self::DEFAULT_LOGO);
            }
        }

        return $path;
    }
}
