<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Model\Renderer\WidgetDirective;

class Wrapper extends \Amasty\MegaMenuPageBuilder\Model\Di\Wrapper
{
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManagerInterface)
    {
        parent::__construct($objectManagerInterface, 'Magento\PageBuilder\Model\Stage\Renderer\WidgetDirective');
    }
}
