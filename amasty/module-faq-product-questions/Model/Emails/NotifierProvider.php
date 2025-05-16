<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Emails;

class NotifierProvider
{
    public const TYPE_ADMIN = 'admin';
    public const TYPE_CUSTOMER = 'customer';

    /**
     * @var array
     */
    private $notifiers;

    public function __construct(
        array $notifiers = []
    ) {
        $this->initializeNotifiers($notifiers);
    }

    public function get(string $type): ?Notifier\NotifierInterface
    {
        return $this->notifiers[$type] ?? null;
    }

    private function initializeNotifiers(array $notifiers): void
    {
        foreach ($notifiers as $type => $notifier) {
            if (!$notifier instanceof Notifier\NotifierInterface) {
                throw new \LogicException(sprintf('Notifier must implement %s', Notifier\NotifierInterface::class));
            }
            $this->notifiers[$type] = $notifier;
        }
    }
}
