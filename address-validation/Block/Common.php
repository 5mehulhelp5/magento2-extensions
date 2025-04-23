<?php
/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2018 Azaleasoft (https://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
namespace Azaleasoft\Asaddressvalidation\Block;

class Common extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}