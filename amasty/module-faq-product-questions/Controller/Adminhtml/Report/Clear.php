<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Controller\Adminhtml\Report;

use Amasty\Faq\Controller\Adminhtml\AbstractReports;
use Amasty\Faq\Model\VisitStatRepository;
use Magento\Backend\App\Action;

class Clear extends AbstractReports
{
    /**
     * @var VisitStatRepository
     */
    private $visitStatRepository;

    public function __construct(
        Action\Context $context,
        VisitStatRepository $visitStatRepository
    ) {
        parent::__construct($context);

        $this->visitStatRepository = $visitStatRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->visitStatRepository->deleteAll();

        if ($result) {
            $this->messageManager->addSuccessMessage(__('Grid has been cleared.'));
        } else {
            $this->messageManager->addErrorMessage(__('An error has occured.'));
        }

        return $this->resultRedirectFactory->create()->setPath('amastyfaq/report/index/');
    }
}
