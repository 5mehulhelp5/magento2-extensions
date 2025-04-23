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
namespace Milople\Depositpayment\Block\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Quote\Api\CartManagementInterface;

class Installments extends \Magento\Framework\View\Element\Template
{
	public $orderId;
	public $tempQuote;
	protected $_quote;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		 CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Catalog\Model\Product $product,
		\Magento\Quote\Model\QuoteFactory $quote,
		\Magento\Framework\Pricing\Helper\Data $priceHelperData,
		\Magento\Payment\Model\MethodList $methodList,
		\Milople\Depositpayment\Helper\EmailSender $emailSender,
		\Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
		\Magento\Customer\Model\Metadata\FormFactory $customerFormFactory,
		\Magento\Checkout\Model\CompositeConfigProvider $configProvider,
		\Magento\Customer\Model\Address\Mapper $addressMapper,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\Session $session,
        \Milople\Depositpayment\Model\PartialpaymentInstallments $partialInstallment,
        \Milople\Depositpayment\Model\PartialpaymentOrders $partialPaymentOrders,
        \Magento\Sales\Model\Order $order,
		array $layoutProcessors = [],
        array $data = []
    ) {
		 $this->customerRepository = $customerRepository;
        $this->quoteRepository = $quoteRepository;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
		$this->request = $request;
		$this->storeManager = $context->getStoreManager();
		$this->customerFactory = $customerFactory;
		$this->customerRepository = $customerRepository;
		$this->_product = $product;
		$this->quoteFactory = $quote;
		$this->addressMapper = $addressMapper;
		$this->methodList = $methodList;
		$this->emailSender = $emailSender;
		$this->priceHelper = $priceHelperData;
		$this->paymentMethodManagement = $paymentMethodManagement;
		$this->_customerFormFactory = $customerFormFactory;
 		$this->configProvider = $configProvider;
 		$this->formKey=$formKey;
 		$this->_jsonEncoder = $jsonEncoder;
        $this->partialInstallment=$partialInstallment;
        $this->partialPaymentOrders=$partialPaymentOrders;
        $this->session=$session;
        $this->order=$order;
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
    }
	
    protected function _construct()
    {
		$this->partialpaymentId = $this->request->getParam('partialpayment_id');
		$this->pageConfig->getTitle()->set(__('Installments of Order #').$this->getOrder($this->getPartialpaymentOrder($this->partialpaymentId)->getOrderId())->getIncrementId());
		$this->orderId = $this->getPartialpaymentOrder($this->partialpaymentId)->getOrderId();
		$this->tempQuote=$this->quoteFactory->create()->load($this->getOrder()->getQuoteId()); 
		$customer = $this->customerRepository->getById($this->getCustomer()->getId());
        $this->tempQuote->assignCustomer($customer);
        $this->quoteRepository->save($this->tempQuote);

        $this->tempQuote->setIgnoreOldQty(true);
        $this->tempQuote->setIsSuperMode(true);
        parent::_construct();
    }
     /**
     * Retrieve url for form submiting
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('sales/*/save');
    }
      /**
     * Retrieve url for loading blocks
     *
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('sales/*/loadBlock');
    }
	public function getPaymentMethodTitle($code)
	{
		foreach ($this->paymentMethodManagement->getList($this->tempQuote->getId()) as $paymentMethod) {
        	if($paymentMethod->getCode() == $code)
			{
                return $paymentMethod->getTitle();
			}
		}
		return $code;
	}
	
	public function getCustomer()
    {
        return $this->session->getCustomer();
    }
	
	public function getCollection()
    {
        if (!$this->getData('collection')) {
            $this->setCollection(
               $this->partialInstallment->getCollection()->addFieldToFilter('partial_payment_id',$this->partialpaymentId)
            );
        }
        return $this->getData('collection');
    }
	
	public function getPartialpaymentOrder()
	{
		return $this->partialPaymentOrders->load($this->partialpaymentId);
	}
	
	public function getOrder()
	{
		return $this->order->load($this->orderId);
	}
	
	public function getPaymentMethodList()
	{
		return $this->methodList->getAvailableMethods($this->tempQuote);
	}

	public function getJsLayout()
	{
	      foreach ($this->layoutProcessors as $processor) {
	          $this->jsLayout = $processor->process($this->jsLayout);
	      }
	      return parent::getJsLayout();
	}
	public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }

     /**
     * Get base url for block.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Retrieve form key
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Get order data jason
     *
     * @return string
     */
    public function getOrderDataJson()
    {
        $data = [];
        if ($this->getCustomer()->getId()) {
            $data['customer_id'] =  $this->getCustomer()->getId();
            $data['addresses'] = [];

            $addresses = $this->customerRepository->getById($this->getCustomer()->getId())->getAddresses();

            foreach ($addresses as $address) {
                $addressForm = $this->_customerFormFactory->create(
                    'customer_address',
                    'adminhtml_customer_address',
                    $this->addressMapper->toFlatArray($address)
                );
                $data['addresses'][$address->getId()] = $addressForm->outputData(
                    \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON
                );
            }
        }
        if ($this->getStoreId() !== null) {
            $data['store_id'] = $this->getStoreId();
            $currency = $this->_localeCurrency->getCurrency($this->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['shipping_method_reseted'] = !(bool)$this->getQuote()->getShippingAddress()->getShippingMethod();
            $data['payment_method'] = $this->getQuote()->getPayment()->getMethod();
        }

        return $this->_jsonEncoder->encode($data);
    }
}