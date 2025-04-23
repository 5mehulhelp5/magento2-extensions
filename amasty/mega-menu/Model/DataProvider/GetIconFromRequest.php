<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Model\DataProvider;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Framework\App\RequestInterface;

class GetIconFromRequest
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return array|string|null
     */
    public function execute()
    {
        $useDefault = $this->request->getParam('use_default');

        return isset($useDefault[ItemInterface::ICON]) && $useDefault[ItemInterface::ICON]
            ? null
            : $this->request->getParam(ItemInterface::ICON);
    }
}
