<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output;

use Amasty\PageSpeedTools\Model\Output\ProcessorsResolver as OutputProcessorsResolver;

class OutputChain implements OutputChainInterface
{
    /**
     * @var OutputProcessorsResolver
     */
    private $outputProcessorsResolver;

    public function __construct(
        OutputProcessorsResolver $outputProcessorsResolver
    ) {
        $this->outputProcessorsResolver = $outputProcessorsResolver;
    }

    public function process(string &$output): bool
    {
        $result = true;
        if ($pageProcessors = $this->getSortedPageProcessors()) {
            foreach ($pageProcessors as $processor) {
                if (!$processor->process($output)) {
                    $result = false;
                    break;
                }
            }
        } else {
            $result = false;
        }

        return $result;
    }

    public function getSortedPageProcessors(): array
    {
        $pageProcessors = $this->outputProcessorsResolver->getPageProcessors();
        if (empty($pageProcessors)) {
            return [];
        }

        $result = [];
        foreach ($pageProcessors as $pageProcessorCode => $pageProcessor) {
            if (!isset($pageProcessor['sortOrder'])) {
                new \LogicException('"sortOrder" is not specified for page processor "' . $pageProcessorCode . '"');
            }

            $sortOrder = (int)$pageProcessor['sortOrder'];
            if (!isset($result[$sortOrder])) {
                $result[$sortOrder] = [];
            }

            $result[$sortOrder][$pageProcessorCode] = $pageProcessor['processor'];
        }

        if (empty($result)) {
            return [];
        }

        ksort($result);

        return array_merge(...$result);
    }
}
