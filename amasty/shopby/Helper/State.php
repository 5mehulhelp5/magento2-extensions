<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Improved Layered Navigation Base for Magento 2
 */

namespace Amasty\Shopby\Helper;

use Magento\Framework\App\Helper\Context;
use Amasty\ShopbyBase\Api\UrlBuilderInterface;

class State extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var UrlBuilderInterface
     */
    private $amUrlBuilder;

    public function __construct(
        Context $context,
        UrlBuilderInterface $amUrlBuilder
    ) {
        parent::__construct($context);
        $this->amUrlBuilder = $amUrlBuilder;
    }

    /**
     * @return mixed
     */
    public function getCurrentUrl()
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = [
            '_' => null,
            'shopbyAjax' => null,
            'price-ranges' => null,
            'dt' => null,
            'df' => null,
            'shopbyCounterAjax' => null
        ];

        $result = str_replace('&amp;', '&', $this->amUrlBuilder->getUrl('*/*/*', $params));
        return $result;
    }
}
