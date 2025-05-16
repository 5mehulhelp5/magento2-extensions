<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\SpeedSize\Client;

use Amasty\ImageOptimizerSpeedSize\Model\ConfigProvider;

class Configuration
{
    private const API_URL = 'https://api.speedsize.com/api';
    private const USER_AGENT = 'Swagger-Codegen/3.0.75/php';
    private const TIMEOUT = 120;

    /**
     * @var array
     */
    private $config = [];

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getUrl(): string
    {
        return self::API_URL;
    }

    public function getAuth(): array
    {
        return $this->config['auth'] ?? [];
    }

    public function getUserAgent(): string
    {
        return $this->config['user_agent'] ?? self::USER_AGENT;
    }

    public function getTimeout(): int
    {
        return $this->config['timeout'] ?? self::TIMEOUT;
    }
}
