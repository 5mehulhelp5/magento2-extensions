<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Controller\Stat;

use Amasty\Faq\Api\VisitStatRepositoryInterface;
use Amasty\Faq\Model\VisitStatFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Visitor;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Collect extends \Magento\Framework\App\Action\Action
{
    /**
     * @var VisitStatRepositoryInterface
     */
    private $visitStatRepository;

    /**
     * @var VisitStatFactory
     */
    private $visitStatFactory;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        VisitStatRepositoryInterface $visitStatRepository,
        VisitStatFactory $visitStatFactory,
        Visitor $visitor,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->visitStatRepository = $visitStatRepository;
        $this->visitStatFactory = $visitStatFactory;
        $this->visitor = $visitor;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute(): void
    {
        $params = $this->getRequest()->getParams();

        if (empty($params['search_query']) && empty($params['category_id']) && empty($params['question_id'])) {
            return;
        }

        $visitStat = $this->visitStatFactory->create();
        /** @var \Amasty\Faq\Model\VisitStat $visitStat */
        $visitStat->addData($params);

        if ($this->customerSession->getCustomerId()) {
            $visitStat->setCustomerId($this->customerSession->getCustomerId());
        } else {
            $visitStat->setVisitorId($this->visitor->getId());
        }

        $visitStat->setStoreId($this->storeManager->getStore()->getId());

        try {
            $this->visitStatRepository->save($visitStat);
        } catch (LocalizedException $e) {
            null;
        }
    }
}
