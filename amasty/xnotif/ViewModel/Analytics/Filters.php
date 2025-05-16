<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\ViewModel\Analytics;

use Amasty\Base\Model\Serializer;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Filters implements ArgumentInterface
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $promoLink;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        string $moduleName,
        string $promoLink,
        Manager $moduleManager,
        Serializer $serializer
    ) {
        $this->moduleName = $moduleName;
        $this->promoLink = $promoLink;
        $this->moduleManager = $moduleManager;
        $this->serializer = $serializer;
    }

    public function getSubscribeUrl(): string
    {
        return $this->promoLink;
    }

    public function hasSubscriptionModule(): bool
    {
        return $this->moduleManager->isEnabled($this->moduleName);
    }

    public function getCalendarConfig(): string
    {
        return $this->serializer->serialize(
            [
                'calendar' => [
                    'dateFormat' => 'y-MM-dd',
                    'showsTime' => false,
                    'timeFormat' => null,
                    'buttonImage' => null,
                    'buttonText' => __('Select Date'),
                    'disabled' => null
                ]
            ]
        );
    }
}
