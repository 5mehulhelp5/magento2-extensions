<?php

declare(strict_types = 1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Model\DataProvider;

use Magento\Framework\App\RequestInterface;

class GetParentIdFromRequest
{
    public const PARENT_CATEGORY_ID_PARAM = 'parent';

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function execute(): int
    {
        return (int) $this->request->getParam(self::PARENT_CATEGORY_ID_PARAM);
    }
}
