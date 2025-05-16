<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output\RequestChecker;

use Magento\Framework\App\RequestInterface;

class EsiBlocksChecker implements RequestCheckerInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function check(): bool
    {
        return strpos($this->request->getOriginalPathInfo(), '/esi/') !== false
            && $this->request->getParam('handles')
            && $this->request->getParam('blocks');
    }
}
