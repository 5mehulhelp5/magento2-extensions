<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Product;

use Amasty\Xnotif\Model\ConfigProvider;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;

class GdprProcessor
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    public function __construct(
        ConfigProvider $configProvider,
        Session $session,
        RequestInterface $request,
        EventManagerInterface $eventManager
    ) {
        $this->configProvider = $configProvider;
        $this->session = $session;
        $this->request = $request;
        $this->eventManager = $eventManager;
    }

    /**
     * @param array $data
     *
     * @throws LocalizedException
     */
    public function validateGDRP(array $data): void
    {
        if ($this->configProvider->isGDPREnabled()
            && !$this->session->isLoggedIn()
            && (!isset($data['gdrp']) || !$data['gdrp'])
        ) {
            throw new LocalizedException(__('Please agree to the Privacy Policy'));
        }
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function logGdpr(?string $email): void
    {
        if ($email) {
            $params = $this->request->getParams();
            $params['email'] = $email;
            $this->request->setParams($params);

            $this->eventManager->dispatch(
                'custom_checkbox_confirm_log',
                ['customer' => $this->session->getCustomer()]
            );
        }
    }
}
