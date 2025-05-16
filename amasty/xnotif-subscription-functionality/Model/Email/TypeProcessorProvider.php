<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out Of Stock Notification Subscription Functionality
 */

namespace Amasty\XnotifSubscriptionFunctionality\Model\Email;

use Amasty\XnotifSubscriptionFunctionality\Model\Email\Type\NotificationTypeInterface;
use Magento\ProductAlert\Block\Email\AbstractEmail;

class TypeProcessorProvider
{
    /**
     * @var NotificationTypeInterface[]
     */
    private $processors;

    /**
     * @param array $notificationTypes [ 'type' =>
     * ['processor' => ProcessorClass, 'urlGenerator' => UrlGeneratorClass], ...
     * ]
     */
    public function __construct(
        array $notificationTypes = []
    ) {
        $this->initializeNotificationTypes($notificationTypes);
    }

    public function getProcessorByType(string $type): ?NotificationTypeInterface
    {
        return $this->processors[$type]['processor'] ?? null;
    }

    public function getUnsubscribeUrlByType(string $type): ?AbstractEmail
    {
        return $this->processors[$type]['urlGenerator'] ?? null;
    }

    private function initializeNotificationTypes(array $notificationTypes): void
    {
        foreach ($notificationTypes as $type => $processors) {
            if (!$processors['processor'] instanceof NotificationTypeInterface) {
                throw new \LogicException(
                    sprintf('Notification type must implement %s', NotificationTypeInterface::class)
                );
            }
            if (!$processors['urlGenerator'] instanceof AbstractEmail) {
                throw new \LogicException(
                    sprintf('Url Generator type must implement %s', AbstractEmail::class)
                );
            }
            $this->processors[$type]['processor'] = $processors['processor'];
            $this->processors[$type]['urlGenerator'] = $processors['urlGenerator'];
        }
    }
}
