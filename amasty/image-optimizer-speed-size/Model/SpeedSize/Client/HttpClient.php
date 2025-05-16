<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\SpeedSize\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class HttpClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Client $client,
        LoggerInterface $logger,
        Configuration $configuration
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    public function setConfig(array $config = []): void
    {
        $this->configuration->setConfig($config);
    }

    public function request(string $method, string $path, $data = null, $queryParams = null): ?ResponseInterface
    {
        $options = [
            RequestOptions::AUTH => $this->configuration->getAuth(),
            RequestOptions::HEADERS => ['User-Agent' => $this->configuration->getUserAgent()],
            RequestOptions::TIMEOUT => $this->configuration->getTimeout()
        ];
        if ($data) {
            $options[RequestOptions::JSON] = $data;
        }
        if ($queryParams) {
            $options[RequestOptions::QUERY] = $queryParams;
        }

        $response = null;
        try {
            $response = $this->client->request(
                $method,
                $this->configuration->getUrl() . $path,
                $options
            );
        } catch (RequestException $e) {
            $response = $e->getResponse();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $response;
    }

    public function isSuccessResponse(?ResponseInterface $response): bool
    {
        return $response && $response->getStatusCode() === 200;
    }

    public function extractJsonResponse(?ResponseInterface $response): array
    {
        return $response ? (array)json_decode($response->getBody()->getContents(), true) : [];
    }
}
