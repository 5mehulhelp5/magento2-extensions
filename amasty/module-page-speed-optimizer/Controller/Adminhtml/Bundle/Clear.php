<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Controller\Adminhtml\Bundle;

use Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Bundle;
use Magento\Backend\App\Action;

class Clear extends Action
{
    /**
     * @var Bundle
     */
    private $bundleResource;

    public function __construct(Bundle $bundleResource, Action\Context $context)
    {
        parent::__construct($context);
        $this->bundleResource = $bundleResource;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->bundleResource->clear();

        return $this->resultRedirectFactory->create()->setRefererUrl();
    }
}
