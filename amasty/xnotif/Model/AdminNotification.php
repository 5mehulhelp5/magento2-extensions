<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model;

use Amasty\Xnotif\Block\AdminNotify;
use Amasty\Xnotif\Helper\Config;
use Amasty\Xnotif\Model\ResourceModel\AdminNotification\CollectionFactory as NotificationCollectionFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\App\Emulation;
use Psr\Log\LoggerInterface;

class AdminNotification
{
    /**
     * @var NotificationCollectionFactory
     */
    private $notificationsFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AdminNotify
     */
    private $adminNotify;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(
        NotificationCollectionFactory $notificationsFactory,
        Config $config,
        AdminNotify $adminNotify,
        Emulation $appEmulation,
        State $appState,
        LoggerInterface $logger,
        TransportBuilder $transportBuilder
    ) {
        $this->notificationsFactory = $notificationsFactory;
        $this->config = $config;
        $this->adminNotify = $adminNotify;
        $this->appEmulation = $appEmulation;
        $this->appState = $appState;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
    }

    public function sendAdminNotifications(?int $storeId = null)
    {
        $emailTo = $this->getEmailTo($storeId);
        $sender = $this->getAdminNotificationSender($storeId);

        if ($this->isAdminNotificationEnabled($storeId) && $emailTo && $sender) {
            try {
                $this->adminNotify->setSubscriptionCollection(
                    $this->notificationsFactory->create()->getCollection($storeId)
                );

                $this->appEmulation->startEnvironmentEmulation($storeId);
                $subscriptionGrid = $this->appState->emulateAreaCode(
                    Area::AREA_FRONTEND,
                    [$this->adminNotify, 'toHtml']
                );
                $this->appEmulation->stopEnvironmentEmulation();

                $transport = $this->transportBuilder->setTemplateIdentifier(
                    $this->getAdminNotificationTemplate($storeId)
                )->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId
                    ]
                )->setTemplateVars(
                    [
                        'subscriptionGrid' => $subscriptionGrid
                    ]
                )->setFrom(
                    $sender
                )->addTo(
                    $emailTo
                )->getTransport();

                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * @return array|string
     */
    protected function getEmailTo(?int $storeId = null)
    {
        $emailTo = $this->config->getAdminNotificationEmail($storeId);
        if (strpos($emailTo, ',') !== false) {
            /*
             * It's done to bypass the Magento 2.3.3 bug, which makes it impossible to add an array
             * of mail recipients until you add one recipient
             */
            $emailTo = array_map('trim', explode(',', $emailTo));
            $firstReceiver = array_shift($emailTo);
            $this->transportBuilder->addTo($firstReceiver);
        }

        return $emailTo;
    }

    /**
     * @return string
     */
    protected function getAdminNotificationSender(?int $storeId = null)
    {
        return $this->config->getAdminNotificationSender($storeId);
    }

    /**
     * @return string
     */
    protected function getAdminNotificationTemplate(?int $storeId = null)
    {
        return $this->config->getAdminNotificationTemplate($storeId);
    }

    /**
     * @return bool
     */
    protected function isAdminNotificationEnabled(?int $storeId = null)
    {
        return $this->config->isAdminNotificationEnabled($storeId);
    }
}
