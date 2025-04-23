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
namespace Milople\Depositpayment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
		
		//create Partial Payment Order Table
		$table = $installer->getConnection()->newTable($installer->getTable('partial_payment_orders'))
				->addColumn('partial_payment_id', 		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, 	['unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true], 'partial payment id')
				->addColumn('order_id',					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 10, 	['nullable' => false], 	'order id')
				->addColumn('is_preordered',			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,	1, 	['nullable' => false,'default' => 0], 'check preorder')	
				->addColumn('total_installments', 		\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, 	['nullable' => false],	'total installments')	
				->addColumn('paid_installments', 		\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3,	['nullable' => false], 	'paid installments')
				->addColumn('remaining_installments', 	\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, 	['nullable' => false],	'remaining installments')
				->addColumn('total_amount',				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2],	['nullable' => false], 	'order total amount')
				->addColumn('paid_amount', 				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2],	['nullable' => false], 	'paid amount')
				->addColumn('remaining_amount',			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], ['nullable' => false],	'remaining amount');
		$installer->getConnection()->createTable($table);
        
		// create Partial Payment Installment Table
		$table = $installer->getConnection()->newTable($installer->getTable('partial_payment_installments')) 
				->addColumn('installment_id',							\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,	10,		['unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true], 'installment payment id')
				->addColumn('partial_payment_id', 						\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 	10, 	['nullable' => false],	'partial payment id')
				->addColumn('installment_amount', 						\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], 	['nullable' => false],	'installment amount')	
				->addColumn('installment_due_date',						\Magento\Framework\DB\Ddl\Table::TYPE_DATE,		null, 	[], 					'installments due date')
				->addColumn('installment_paid_date', 					\Magento\Framework\DB\Ddl\Table::TYPE_DATE, 	null, 	[], 					'installment paid date')
				->addColumn('installment_status', 						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 	10, 	['nullable' => false,'default' => 'Remaining'], 'It can be Paid, Remaining, Canceled and Failed.')
				->addColumn('payment_method', 							\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 	255, 	['nullable' => false],	'payment method')
				->addColumn('transaction_id', 							\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 	255, 	['nullable' => false], 	'transaction id')
				->addColumn('installment_reminder_email_sent', 			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, 		['nullable' => false,'default' => 0],	'installment reminder email sent')
				->addColumn('installment_over_due_notice_email_sent', 	\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, 		['nullable' => false,'default' => 0], 	'installment over due notice email sent');
		$installer->getConnection()->createTable($table);
		
		// create Partial Payment Installment Table
		$table = $installer->getConnection()->newTable($installer->getTable('partial_payment_orders_products')) 
				->addColumn('partial_payment_order_product_id',	\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,	10,		['unsigned' => true, 'nullable' => false, 'primary' => true,'auto_increment' => true], 'partial payment order product id')
				->addColumn('partial_payment_id', 				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 	10, 	['nullable' => false],	'partial payment id')
				->addColumn('sales_flat_orders_item_id', 		\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 	10, 	['nullable' => false],	'sales flat orders item id')
				->addColumn('total_installments',   			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, 		['nullable' => false], 	'total installments')
				->addColumn('paid_installments', 				\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3, 		['nullable' => false], 'paid installments')
				->addColumn('remaining_installments', 			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 3,		['nullable' => false],	'remaining installments')
				->addColumn('downpayment', 						\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], 	['nullable' => false], 	'Down Payment')
				->addColumn('total_amount', 					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], 	['nullable' => false], 	'total amount')
				->addColumn('paid_amount',  					\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], 	['nullable' => false],	'paid amount')
				->addColumn('remaining_amount',  				\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 	[12,2], 	['nullable' => false], 	'remaining amount')
				->addColumn('product_instock_email_sent', 		\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 1, 		['nullable' => false,'default' => 0], 'Product in-stock mail sent');
		$installer->getConnection()->createTable($table);
				
		//alter Quote Address table
		$eavTable = $installer->getTable('quote_address');
		$installer->getConnection()->addColumn($eavTable,'paid_amount',			 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_paid_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'remaining_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Remaining Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_remaining_amount',['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Remaining Amount',]);
		
		//alter Sales Order table
		$eavTable = $installer->getTable('sales_order');
		$installer->getConnection()->addColumn($eavTable,'paid_amount',			 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_paid_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'remaining_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Remaining Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_remaining_amount',['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Remaining Amount',]);
		
		//alter Sales Invoice table
		$eavTable = $installer->getTable('sales_invoice');
		$installer->getConnection()->addColumn($eavTable,'paid_amount',			 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_paid_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'remaining_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Remaining Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_remaining_amount',['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Remaining Amount',]);
		
		//alter Sales Creditmemo table
		$eavTable = $installer->getTable('sales_creditmemo');
		$installer->getConnection()->addColumn($eavTable,'paid_amount',			 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_paid_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Paid Amount',]);
		$installer->getConnection()->addColumn($eavTable,'remaining_amount',	 ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Remaining Amount',]);
		$installer->getConnection()->addColumn($eavTable,'base_remaining_amount',['type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,2', 'nullable' => false,'default' => 0, 'comment' => 'Base Remaining Amount',]);
	}
}