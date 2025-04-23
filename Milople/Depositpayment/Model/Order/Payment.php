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
namespace Milople\Depositpayment\Model\Order;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Info;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;

/**
 * Order payment information
 *
 * @method \Magento\Sales\Model\ResourceModel\Order\Payment _getResource()
 * @method \Magento\Sales\Model\ResourceModel\Order\Payment getResource()
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Payment extends \Magento\Sales\Model\Order\Payment
{
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        ManagerInterface $transactionManager,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Sales\Model\Order\Payment\Processor $paymentProcessor,
        OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $encryptor,
			$creditmemoFactory,
			$priceCurrency,
        	$transactionRepository,
        	$transactionManager,
        	$transactionBuilder,
        	$paymentProcessor,
        	$orderRepository,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Authorize or authorize and capture payment on gateway, if applicable
     * This method is supposed to be called only when order is placed
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function place()
    {
        $this->_eventManager->dispatch('sales_order_payment_place_start', ['payment' => $this]);
        $order = $this->getOrder();
	      
    }
}
