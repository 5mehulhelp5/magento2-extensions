<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output;

use Amasty\PageSpeedTools\Model\Output\RequestChecker\RequestCheckerInterface;

class ProcessorsResolver
{
    /**
     * @var OutputProcessorInterface[]
     */
    private $customProcessors;

    /**
     * @var OutputProcessorInterface[]
     */
    private $defaultProcessors;

    public function __construct(
        array $customProcessors,
        array $defaultProcessors
    ) {
        $this->initialize($customProcessors);
        $this->defaultProcessors = $defaultProcessors;
    }

    public function getPageProcessors(): array
    {
        foreach ($this->customProcessors as $config) {
            if ($config['checker']->check()) {
                return $config['processors'];
            }
        }

        return $this->defaultProcessors;
    }

    private function initialize(array $customProcessors): void
    {
        foreach ($customProcessors as $name => $config) {
            if (isset($config['checker']) && !$config['checker'] instanceof RequestCheckerInterface) {
                throw new \LogicException(sprintf(
                    'Request processor checker "%s" must be instance of %s',
                    $name,
                    RequestCheckerInterface::class
                ));
            }
        }

        $this->customProcessors = $customProcessors;
    }
}
