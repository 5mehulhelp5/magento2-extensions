<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Observer;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\Gdpr\ConsentsProcessor;
use Amasty\Faq\Model\ThirdParty\ModuleChecker;
use Amasty\Gdpr\Model\Consent\RegistryConstants;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class GdprConsentLog implements ObserverInterface
{
    /**
     * @var ConsentsProcessor
     */
    private $consentsProcessor;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Session
     */
    private $customerSession;

    public function __construct(
        ConsentsProcessor $consentsProcessor,
        ModuleChecker $moduleChecker,
        RequestInterface $request,
        Session $customerSession
    ) {
        $this->consentsProcessor = $consentsProcessor;
        $this->moduleChecker = $moduleChecker;
        $this->request = $request;
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        if ($this->moduleChecker->isAmastyGdprEnabled()) {
            /** @var  \Amasty\Faq\Model\Question $question */
            $question = $observer->getEvent()->getQuestion();
            $storeId = $question->getAskedFromStore();
            $customerId = (int)$this->customerSession->getId();
            $email = $this->request->getParam(QuestionInterface::EMAIL);
            $consentsData = $this->request->getParam(RegistryConstants::CONSENTS);

            if ($consentsData) {
                $this->consentsProcessor->process($storeId, $customerId, $email, $consentsData);
            }
        }
    }
}
