<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Premium Base for Magento 2
 */

namespace Amasty\MegaMenuPremium\Plugin\Widget\Model\Widget;

use Amasty\MegaMenuPremium\Model\DataProvider\GetAllowedWidgets;
use Magento\Framework\App\RequestInterface;
use Magento\Widget\Model\Widget;

class RemoveForbiddenWidgets
{
    public const WIDGET_TARGET_ID = 'widget_target_id';

    public const AM_MEGAMENU_MOBILE_CONTENT = [
        'category_form_mobile_content',
        'catalogstaging_category_update_form_mobile_content',
        'amasty_megamenu_link_form_mobile_content'
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetAllowedWidgets
     */
    private $getAllowedWidgets;

    public function __construct(
        RequestInterface $request,
        GetAllowedWidgets $getAllowedWidgets
    ) {
        $this->request = $request;
        $this->getAllowedWidgets = $getAllowedWidgets;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetWidgets(Widget $subject, array $widgets): array
    {
        if (in_array($this->request->getParam(self::WIDGET_TARGET_ID), self::AM_MEGAMENU_MOBILE_CONTENT)) {
            $widgets = array_intersect_ukey(
                $widgets,
                $this->getAllowedWidgets->execute(),
                function ($key1, $key2) {
                    return $key1 <=> $key2;
                }
            );
        }

        return $widgets;
    }
}
