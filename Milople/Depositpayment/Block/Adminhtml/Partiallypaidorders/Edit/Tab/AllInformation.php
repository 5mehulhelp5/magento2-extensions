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
* @url         https://www.milople.com/magento2-extensions/desposit-payment-m2.html
*
**/
namespace Milople\Depositpayment\Block\Adminhtml\Partiallypaidorders\Edit\Tab;

class AllInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	public $tempQuote;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $_systemStore;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    public $_wysiwygConfig;
 
    public $_status;
    protected $_template = 'all_information.phtml';
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
		\Magento\Quote\Model\QuoteFactory $quote,
        \Milople\Depositpayment\Model\PartialpaymentOrders $partialPaymentOrders,
        \Milople\Depositpayment\Model\PartialpaymentProducts $partialProducts,
        \Milople\Depositpayment\Model\PartialpaymentInstallments $partialInstallment,
        \Magento\Sales\Model\Order\Item $item,
        \Magento\Sales\Model\Order $order,
        \Milople\Depositpayment\Model\InstallmentStatus $installmentStatus,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Pricing\Helper\Data $dataPriceHelper,
		\Magento\Backend\Model\UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
		$this->quote = $quote;
		$this->urlBuilder = $urlBuilder;
        $this->partialPaymentOrders=$partialPaymentOrders;
        $this->partialProducts=$partialProducts;
        $this->partialInstallment=$partialInstallment;
        $this->order=$order;
        $this->installmentStatus=$installmentStatus;
        $this->dataPriceHelper=$dataPriceHelper;
        $this->item=$item;
        $this->product=$product;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
	public function _construct()
	{
 		$this->partialpayment_id = $this->getRequest()->getParam('id');
		$this->priceHelper =  $this->dataPriceHelper; // Instance of Pricing Helper
		$this->tempQuote=$this->quote->create()->loadByIdWithoutStore($this->getOrderCollection($this->getCollection()->getOrderId())->getQuoteId()); //Create object of quote
	}
 
    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        return parent::_prepareForm();
    }
 
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('All Information');
    }
 
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('All Information');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    public function getCollection()
    {
        if (!$this->getData('collection')) {
            $this->setCollection($this->partialPaymentOrders->load($this->partialpayment_id)
            );
        
        }
        return $this->getData('collection');
    }
	public function getProductCollection()
    {
        return  $this->partialProducts->getCollection()
                                      ->addFieldToFilter('partial_payment_id',$this->partialpayment_id);
    }
	public function getInstallmentsCollection()
    {
        return  $this->partialInstallment->getCollection()
                                         ->addFieldToFilter('partial_payment_id',$this->partialpayment_id);
    }
    public function getOrderCollection($orderId)
    {
        return $this->order->load($orderId);
    }
    public function getOrderItemCollection($itemId)
    {
        return $this->item->load($itemId);
    }
    public function getProductInformation($productId)
    {
        return $this->product->load($productId);
    }
}