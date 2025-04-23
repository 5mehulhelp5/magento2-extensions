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
namespace Milople\Depositpayment\Controller\Adminhtml\Reports;
use \Magento\Backend\App\Action;
class MassInstallmentsPay extends Action{
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Milople\Depositpayment\Model\PartialpaymentInstallments $installmentsFactory,
		\Milople\Depositpayment\Model\Calculation $calculation
    ) {
        parent::__construct($context);
		$this->installmentsFactory = $installmentsFactory;		
		$this->calculation = $calculation;
    }
    public function execute()
    {
		try
		{	
			$allInstallments = $this->_request->getParams();
			$payment = $allInstallments['pay'];
			$allInstallments = $allInstallments['partialpaymentInstallments'];		
			// call email sending function to send reminder mail
			foreach($allInstallments as $installmentId)
			{
				$installment = $this->installmentsFactory->load($installmentId);
				# does installment already paid?
				if($installment->getInstallmentStatus() != 'Paid')
				{
					# set installment success data in table
					$this->calculation->setInstallmentSuccessData($installmentId,$payment);
				}
			}
			$this->messageManager->addSuccess(__("Installment(s) Successfully paid."));
		}
		catch(\Exception $e)
		{
			$this->messageManager->addError($e->getMessage());			
		}
		$this->_redirect('*/*/installments');
    }
}