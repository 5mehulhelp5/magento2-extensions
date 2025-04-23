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
namespace Milople\Depositpayment\Observer\Frontend;
 
use Magento\Framework\Exception\LocalizedException;
 
class CartPlugin
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    protected $request;
 
    /**
     * Plugin constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Psr\Log\LoggerInterface $logger
        ) {
        $this->quote = $checkoutSession->getQuote();
        $this->_productRepository = $productRepository;
        $this->request = $request; 
        $this->logger=$logger;
        $this->resultRedirect = $result;
    }
 
    /**
     * beforeAddProduct
     *
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {        
        $productId = (int)$this->request->getParam('product', 0);        
        $_product = $this->_productRepository->getById($productId);
        $postdata = $this->getRequest()->getPost();
        if(!isset($postdata['allow_partial_payment']))
        {
            $this->request->setParam('product', false); 
            $this->request->setParam('return_url', $_product->getProductUrl());      
        }       
        return [$productInfo, $requestInfo];
    }
     
}