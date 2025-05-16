<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Controller\Adminhtml\DiagnosticResult;

use Amasty\PageSpeedOptimizer\Api\DiagnosticResultRepositoryInterface;
use Amasty\PageSpeedOptimizer\Model\DiagnosticResult\DiagnosticResult;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Load extends Action
{
    public const ADMIN_RESOURCE = 'Amasty_PageSpeedOptimizer::config';

    /**
     * @var DiagnosticResultRepositoryInterface
     */
    private $diagnosticResultRepository;

    public function __construct(
        Context $context,
        DiagnosticResultRepositoryInterface $diagnosticResultRepository
    ) {
        parent::__construct($context);
        $this->diagnosticResultRepository = $diagnosticResultRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($this->diagnosticResultRepository->getListResults());

        return $result;
    }
}
