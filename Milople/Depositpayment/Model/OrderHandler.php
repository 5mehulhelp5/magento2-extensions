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
namespace Milople\Depositpayment\Model;

class OrderHandler extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Catalog\Model\Product $product
    * @param Magento\Framework\Data\Form\FormKey $formKey $formkey,
    * @param Magento\Quote\Model\Quote $quote,
    * @param Magento\Customer\Model\CustomerFactory $customerFactory,
    * @param Magento\Sales\Model\Service\OrderService $orderService,
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		//\Magento\Authorizenet\Model\Directpost $directpost,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Product $product,
		\Magento\Framework\Data\Form\FormKey $formkey,
		\Magento\Quote\Model\QuoteFactory $quote,
		\Magento\Quote\Model\QuoteManagement $quoteManagement,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Magento\Sales\Model\Service\OrderService $orderService,
	    \Magento\Framework\DataObject $payment
    ) {
        $this->_storeManager = $storeManager;
		//$this->directpost = $directpost;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
	    $this->payment = $payment;
        parent::__construct($context);
    }
 
    /**
     * Create Order On Your Store
     * 
     * @param array $orderData
     * @return array
     * 
    */
    public function createMageOrder($payment) {
		$orderData=[
             'currency_id'  => 'USD',
             'email'        => 'test@milople.com', //buyer email id
             'shipping_address' =>[
             'firstname'    => 'jhon', //address Details
             'lastname'     => 'Deo',
             'street' => 'xxxxx',
             'city' => 'xxxxx',
             'country_id' => 'IN',
             'region' => 'xxx',
             'postcode' => '43244',
             'telephone' => '52332',
             'fax' => '32423',
             'save_in_address_book' => 1
            ],
           'items'=> [ //array of product which order you want to create
                ['product_id'=>'1','qty'=>1, 'price' => 10, 'type_id' => 'virtual'],
                ['product_id'=>'2','qty'=>2, 'price' => 20, 'type_id' => 'virtual']
            ]
        ];
         $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customet by email address
        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        }
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
 
        //add items in quote
        foreach($orderData['items'] as $item){
            $product=$this->_product->load($item['product_id']);
            $product->setPrice($item['price']);
            $quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }
 
        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
 
        // Collect Rates and Set Shipping & Payment Method
 
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('flatrate_flatrate'); //shipping method
        $quote->setPaymentMethod($payment['method']); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready
 
        // Set Sales Order Payment
        $quote->getPayment()->importData($payment);
 
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
 
        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);
        
        $order->setEmailSent(0);
        $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'Your custom message'];
        }
        return $result;
    }
	public function order2($cartId)
	{
		$quote=$this->quote->create();
		$quote->load($cartId); 

		if($quote->getCustomerEmail() == NULL){
			$quote->setCustomerId(null)
				->setCustomerEmail('bhargavjoshi@milople.com')
				->setCustomerIsGuest(true)
				->setCustomerGroupId(0);
		}
		//Set Billing/Shipping Address
		$addressData = $this->_getAddressData(true);
		$quote->getBillingAddress()->addData($addressData);
		$quote->getShippingAddress()->addData($addressData);


		//Set Shipping Method

		$quote->getShippingAddress()->setShippingMethod($this->_shippingMethod);
		$quote->getPayment()->importData(array('method' => $this->_getPaymentMethod()));
		$quote->setPaymentMethod($this->_getPaymentMethod());
		$quote->setInventoryProcessed(false);
		try{
			$quote->save();
			$quote->getShippingAddress()->setCollectShippingRates(true);
			$quote->getShippingAddress()->collectShippingRates();
			$quote->collectTotals();

			$quote->save();
			$quoteManagement = $this->quoteManagement; 
			$order = $quoteManagement->submit($quote);
			$order->setEmailSent(0);
    		$order->getPayment()->setLastTransId($amazonOrderId)->save();

			 /** @var $checkoutSession \Magento\Checkout\Model\Session */
			$checkoutSession = $this->getModel('Magento\Checkout\Model\Session');
			$checkoutSession->setQuoteId($quote->getId());
			$checkoutSession->setLoadInactive(true);

			$checkoutSession->setLastOrderId($order->getId())
					->setLastRealOrderId($order->getIncrementId());

			$quote->setIsActive(false)->save();

			$this->eventManager = $this->getModel('Magento\Framework\Event\ManagerInterface');
			$this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);
        	if($response != ''){
				$this->updateOrderIOPN($order->getId(), $response);
			}
			$this->setSuccessData(null, $order);
		
		}
		catch(\Exception $e){
		
		}
		return $this;
	}
	// public function testCallForAuthorizenet()
	// {
	// 	$this->directpost->testCall();
	// }
	public function directCall(\Magento\Sales\Model\Order $order,$payment)
	{
		$order->setIncrementId($order->getIncrementId());
		$this->payment->setOrder($order);
		$this->payment->setCcNumber($payment['cc_number']);
		$this->payment->setCcExpMonth($payment['cc_exp_month']);
		$this->payment->setCcExpYear($payment['cc_exp_year']);
		$this->payment->setCcCid('111');
		//$this->directpost->testCall($order,$this->payment);
	}
}
