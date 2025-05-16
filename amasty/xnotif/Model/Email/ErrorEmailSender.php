<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Email;

use Amasty\Xnotif\Model\ConfigProvider;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class ErrorEmailSender
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * Send error emails to administrator
     *
     * @param array $errors
     * @return void
     */
    public function execute(array $errors): void
    {
        $errorTemplate = $this->configProvider->getErrorTemplate();
        $recipient = $this->configProvider->getErrorRecipient();

        if (!count($errors) || !$errorTemplate || !$recipient) {
            return;
        }

        $this->logger->error(
            'Amasty OSN: ' . count($errors) . ' errors occurred during sending alerts.'
        );

        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $errorTemplate
        )->setTemplateOptions(
            [
                'area' => FrontNameResolver::AREA_CODE,
                'store' => Store::DEFAULT_STORE_ID
            ]
        )->setTemplateVars(
            ['warnings' => join("\n", $errors)]
        )->setFromByScope(
            $this->configProvider->getErrorIdentity()
        )->addTo(
            $recipient
        )->getTransport();

        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
