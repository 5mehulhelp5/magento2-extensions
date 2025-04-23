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
namespace Milople\Depositpayment\Controller\Adminhtml\Partiallypaidorders;
use Magento\Backend\App\Action;
use \AllowDynamicProperties;
#[AllowDynamicProperties]
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
 
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Milople\Depositpayment\Model\PartialpaymentOrders $partialPaymentOrders,
        \Magento\Framework\Registry $registry
        ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->partiallypaidorders=$partialPaymentOrders;
        $this->_coreRegistry = $registry;
        $this->session=$context->getSession();
       parent::__construct($context);
    }
 
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }
 
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Milople_Depositpayment::partiallypaidorders');
        return $resultPage;
    }
 
    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
         
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->partiallypaidorders;
 
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This grid record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
 
        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
 
        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('milople_form_data', $model);
 
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__('Edit Deposit Payment Order'),__('Edit Deposit Payment Order'));
				$title = __('Edit Deposit Payment Order');		
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}