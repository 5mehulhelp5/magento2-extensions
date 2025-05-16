<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Gdpr;

use Amasty\Faq\Model\Config\Source\Gdpr\CheckboxLocation;
use Amasty\Gdpr\Model\Consent\RegistryConstants;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface;

class ConsentsProcessor
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ManagerInterface $eventManager,
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger
    ) {
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    public function process($storeId, $customerId, $email, $consentsData)
    {
        $consentsData = $this->groupConsentsData($consentsData);
        try {
            foreach ($consentsData as $from => $consentCodes) {
                $this->eventManager->dispatch(
                    'amasty_gdpr_consent_accept',
                    [
                        RegistryConstants::CONSENTS => $consentCodes,
                        RegistryConstants::CONSENT_FROM => $from,
                        RegistryConstants::CUSTOMER_ID => $customerId,
                        RegistryConstants::STORE_ID => (int)$storeId,
                        RegistryConstants::EMAIL => $email
                    ]
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    private function groupConsentsData(array $consentsData): array
    {
        $grouped = [];

        foreach ($consentsData as $consentCode => $consent) {
            $from = $consent['from'] ?? CheckboxLocation::FAQ_QUESTION_FORM;
            $checked = $consent['checked'] ?? $consent;
            $grouped[$from][$consentCode] = $checked;
        }

        return $grouped;
    }
}
