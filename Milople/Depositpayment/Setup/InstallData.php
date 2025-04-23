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

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
	/**
	 * EAV setup factory
	 *
	 * @var EavSetupFactory
	 */
	private $eavSetupFactory;

	/**
	 * Init
	 *
	 * @param EavSetupFactory $eavSetupFactory
	 */
	public function __construct(EavSetupFactory $eavSetupFactory) {
		$this -> eavSetupFactory = $eavSetupFactory;
	}
	/**
	 * {@inheritdoc}.
	 * Function holds all scripts.
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

		/**
		 * @var EavSetup $eavSetup
		 */
		$eavSetup = $this -> eavSetupFactory -> create(['setup' => $setup]);
        $eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'apply_partial_payment', [
		'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Apply Deposit Payment', 'input' => 'select',
		'class' => '', 'source' => 'Milople\Depositpayment\Model\Config\Source\Product\AllowFullPayment',
		'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => 0, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
		
		$eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_full_payment', [
		'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Allow Full Payment?', 'input' => 'select',
		'class' => '', 'source' => 'Milople\Depositpayment\Model\Config\Source\Product\AllowFullPayment',
		'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => 0, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
		
		$eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_flexy_payment', [
		'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Allow Flexy / Layaway Payments?', 'input' => 'select',
		'class' => '', 'source' => 'Milople\Depositpayment\Model\Config\Source\Product\AllowFullPayment',
		'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => 0, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
		
		$eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'no_of_installments', [
		'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Number of Installments', 'input' => 'text',
		'class' => 'validate-number  validate-greater-than-zero','global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => '', 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
		
		$eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'down_payment_calculation', [
		'type' => 'int', 'backend' => '', 'frontend' => '', 'label' => 'Calculate Down Payment On', 'input' => 'select',
		'class' => '', 'source' => 'Milople\Depositpayment\Model\Config\Source\Product\Calculate',
		'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => 0, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
		
		$eavSetup -> addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'downpayment', [
		'type' => 'decimal', 'backend' => '', 'frontend' => '', 'label' => 'Down Payment', 'input' => 'text',
		'class' => 'validate-zero-or-greater', 'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
		'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
		'group' => 'Deposit Payment', 'visible' => true, 'required' => false, 'user_defined' => true,
		'default' => 0, 'searchable' => false, 'filterable' => false, 'comparable' => false, 'visible_on_front' => true, 
		'used_in_product_listing' => true, 'unique' => false, 'apply_to' => ''
		]);
    }
}
