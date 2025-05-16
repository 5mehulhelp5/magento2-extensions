<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Plugin\PageCache\Controller\Block\Esi;

use Amasty\PageSpeedTools\Model\Output\OutputChainInterface;
use Amasty\PageSpeedTools\Plugin\ProcessPageResult;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\PageCache\Controller\Block\Esi;

class ProcessEsiBlocks
{
    /**
     * @var OutputChainInterface
     */
    private $outputChain;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        OutputChainInterface $outputChain,
        ManagerInterface $eventManager
    ) {
        $this->outputChain = $outputChain;
        $this->eventManager = $eventManager;
    }

    public function afterExecute(Esi $subject): void
    {
        /** @var HttpInterface $response */
        $response = $subject->getResponse();
        $html = $response->getContent();

        if ($this->outputChain->process($html)) {
            $response->setBody($html);
        }

        $this->eventManager->dispatch(ProcessPageResult::EVENT_NAME, ['response' => $response]);
    }
}
