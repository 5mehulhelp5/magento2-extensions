<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Html\Header;

use Magento\Framework\View\Element\Template;

class Breadcrumbs extends Template
{
    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * Breadcrumbs constructor.
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setBreadcrumbs(!empty($data['breadcrumbs']) ? $data['breadcrumbs'] : []);
    }

    /**
     * @param array $breadcrumbs
     * @return $this
     */
    public function setBreadcrumbs(array $breadcrumbs = [])
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    /**
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return [
            [
                'title' => __('Home'),
                'url'   => $this->getBaseUrl()
            ],
            [
                'title' => __('Cart'),
                'url'   => $this->getUrl('checkout/cart')
            ],
            [
                'title' => __('Checkout'),
                'url'   => $this->getUrl('mageworx_checkout/onepage')
            ],
        ];
    }
}
