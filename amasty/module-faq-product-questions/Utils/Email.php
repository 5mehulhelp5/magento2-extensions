<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Utils;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Email
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * Send email helper
     * emailTo and sendFrom can be array with keys email and name.
     * Otherwise string with key to Store Email address.
     *
     * @param string|array $emailTo
     * @param string $templateConfigPath
     * @param array  $vars
     * @param string $area
     * @param string|array $sendFrom
     */
    public function sendEmail(
        $emailTo = '',
        $templateConfigPath = '',
        $vars = [],
        $area = \Magento\Framework\App\Area::AREA_FRONTEND,
        $sendFrom = ''
    ) {
        try {
            $storeId = null;
            if (isset($vars['asked_from_store'])) {
                $storeId = $vars['asked_from_store'];
            }
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore($storeId);
            $data =  array_merge(
                [
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(),
                ],
                $vars
            );

            if (empty($sendFrom)) {
                $sendFrom = 'general';
            } else {
                $sendFrom = [
                    'email' => $this->configProvider->getTransIdentGeneralEmail($store->getId()),
                    'name' => $this->configProvider->getTransIdentGeneralName($store->getId())
                ];
            }

            if (!is_array($emailTo)) {
                $emailTo = [
                    'email' => $this->configProvider->getTransIdentEmail($emailTo, $store->getId()),
                    'name' => $this->configProvider->getTransIdentName($emailTo, $store->getId())
                ];
            }

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->configProvider->getTemplateIdentifier($templateConfigPath, $store->getId())
            )->setTemplateOptions(
                ['area' => $area, 'store' => $store->getId()]
            )->setTemplateVars(
                $data
            )->setFrom(
                $sendFrom
            )->addTo(
                $emailTo['email'],
                $emailTo['name']
            )->getTransport();

            $subject = (string) __($transport->getMessage()->getSubject());
            $transport->getMessage()->setSubject($subject);
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
