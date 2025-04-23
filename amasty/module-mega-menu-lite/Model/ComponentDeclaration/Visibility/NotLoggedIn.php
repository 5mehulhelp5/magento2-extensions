<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration\Visibility;

use Amasty\MegaMenuLite\Api\Component\VisibilityInterface;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;

class NotLoggedIn implements VisibilityInterface
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    public function __construct(
        HttpContext $httpContext
    ) {
        $this->httpContext = $httpContext;
    }

    public function isVisible(): bool
    {
        return !$this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
