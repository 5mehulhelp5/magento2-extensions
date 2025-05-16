<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Plugin\Framework\Controller;

use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;

class AddClientHintHeaders
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param ResultInterface $subject
     * @param ResultInterface $result
     * @param ResponseHttp $response
     * @return ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRenderResult(
        ResultInterface $subject,
        ResultInterface $result,
        ResponseHttp $response
    ): ResultInterface {
        if (!$this->configProvider->getSpeedSizeKey()) {
            return $result;
        }

        $cdnUrl = rtrim(trim($this->configProvider->getSpeedSizeCdnUrl()), '/');
        $response->setHeader('Accept-CH', 'viewport-width, width, dpr');
        $response->setHeader(
            'Permissions-Policy',
            "ch-viewport-width=(\"{$cdnUrl}\"), ch-dpr=(\"{$cdnUrl}\"), ch-width=(\"{$cdnUrl}\")"
        );

        return $result;
    }
}
