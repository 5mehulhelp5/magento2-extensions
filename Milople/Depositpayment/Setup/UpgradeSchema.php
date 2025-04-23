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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$setup->startSetup();
		$quoteAddressTable = 'quote_address';
        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';	
        if(version_compare($context->getVersion(), '1.1.0', '<')) {
			//Setup two columns for quote, quote_address and order
			//Quote address tables
			$setup->getConnection()
				->addColumn(
					$setup->getTable($quoteAddressTable),
					'installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Installment Fee'
					]
				);
			$setup->getConnection()
				->addColumn(
				  $setup->getTable($quoteAddressTable),
					'base_installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Base Installment Fee'
					]
				);
			//Quote tables
			$setup->getConnection()
				->addColumn(
					$setup->getTable($quoteTable),
					'installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Installment Fee'
					]
				);
			$setup->getConnection()
				->addColumn(
					$setup->getTable($quoteTable),
					'base_installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Base Installment Fee'
					]
				);
			//Order tables
			$setup->getConnection()
				->addColumn(
					$setup->getTable($orderTable),
					'installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Installment Fee'
					]
				);
			 $setup->getConnection()
				 ->addColumn(
					$setup->getTable($orderTable),
					'base_installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Base Installment Fee'
					]
				);
			//Invoice tables
			$setup->getConnection()
				->addColumn(
					$setup->getTable($invoiceTable),
					'installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Installment Fee'
					]
				);
			$setup->getConnection()
				->addColumn(
					$setup->getTable($invoiceTable),
					'base_installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Base Installment Fee'
					]
				);
			//Credit memo tables
			$setup->getConnection()
				->addColumn(
					$setup->getTable($creditmemoTable),
					'installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Installment Fee'
					]
				);
			$setup->getConnection()
				->addColumn(
					$setup->getTable($creditmemoTable),
					'base_installment_fee',
					[
						'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
						'length' => '10,2',
						'default' => 0.00,
						'nullable' => true,
						'comment' =>'Base Installment Fee'
					]
				);
			$setup->endSetup();
		}
	}
}	