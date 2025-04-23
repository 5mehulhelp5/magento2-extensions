<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/contact-us.html
*
* @category    Ecommerce
* @package     Milople_Depositpayment
* @copyright   Copyright (c) 2017 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url         https://www.milople.com/magento2-extensions/deposit-payment-m2.html
*
**/
namespace Milople\Depositpayment\Block\Product\View\Type;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
class Bundle extends \Magento\Catalog\Block\Product\View\AbstractView
{
	 /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Bundle\Model\Product\PriceFactory $productPrice
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Bundle\Model\Product\PriceFactory $productPrice,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
		\Milople\Depositpayment\Helper\Data $data_helper,
         \Magento\Directory\Model\Currency $currency,
		\Milople\Depositpayment\Helper\Partialpayment $partialpaymentHelper,
        array $data = []
    ) 
	{
		$this->catalogProduct = $catalogProduct;
		$this->productPriceFactory = $productPrice;
		$this->jsonEncoder = $jsonEncoder;
		$this->localeFormat = $localeFormat;
		$this->helper = $data_helper;
        $this->_currency = $currency;   
		$this->partialpaymentHelper = $partialpaymentHelper;
		parent::__construct($context, $arrayUtils, $data);
    }
}
