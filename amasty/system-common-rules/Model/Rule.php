<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Common Rules for Magento 2 (System)
 */

namespace Amasty\CommonRules\Model;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    public const ALL_ORDERS = 0;
    public const BACKORDERS_ONLY = 1;
    public const NON_BACKORDERS = 2;

    public const SALES_RULE_PRODUCT_CONDITION_NAMESPACE = \Magento\SalesRule\Model\Rule\Condition\Product::class;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Rule\Condition\Combine
     */
    protected $conditionCombine;

    /**
     * @var Rule\Condition\Product\Combine
     */
    protected $conditionProductCombine;

    /**
     * @var \Amasty\CommonRules\Model\Modifiers\Subtotal
     */
    protected $subtotalModifier;

    /**
     * @var Validator\Backorder
     */
    protected $backorderValidator;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\CommonRules\Model\Rule\Condition\Combine $conditionCombine,
        \Amasty\CommonRules\Model\Rule\Condition\Product\Combine $conditionProductCombine,
        \Amasty\CommonRules\Model\Modifiers\Subtotal $subtotalModifier,
        \Amasty\CommonRules\Model\Validator\Backorder $backorderValidator,
        $resource = null,
        array $data = []
    ) {
        $this->conditionCombine = $conditionCombine;
        $this->conditionProductCombine = $conditionProductCombine;
        $this->storeManager = $storeManager;
        $this->subtotalModifier = $subtotalModifier;
        $this->backorderValidator = $backorderValidator;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            null,
            $data
        );
    }

    public function getConditionsInstance()
    {
        return $this->conditionCombine;
    }

    public function getActionsInstance()
    {
        return $this->conditionProductCombine;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateResult\Method $rate
     *
     * @return bool
     */
    public function match($rate)
    {
        $selectedCarriers = explode(',', (string)$this->getCarriers());

        if (in_array($rate->getCarrier(), $selectedCarriers)) {
            return true;
        }
        $methods = $this->getMethods();

        if (!$methods) {
            return false;
        }
        $methods = array_unique(explode(',', $methods));

        if($rate->getCarrier()==='matrixrate'){
            if($rate->getMethodTitle() && $rate->getMethodTitle()->getText()){
                $rateCode = $rate->getMethodTitle()->getText();
            }

            /** @var string $methodName */
            foreach ($methods as $methodName) {
                if ('matrixrate_matrixrate_{{'.$rateCode == $methodName) {
                    return true;
                }
            }
        }
        else{
            $rateCode = $rate->getCarrier();
            if (strpos($rate->getCarrier(), '_') === false) {
                $rateCode = $rate->getCarrier() . '_' . $rate->getMethod();
            }

            /** @var string $methodName */
            foreach ($methods as $methodName) {
                if ($rateCode == $methodName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return $this
     */
    protected function _setWebsiteIds()
    {
        $websites = [];

        foreach ($this->storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();

                foreach ($stores as $store) {
                    $websites[$store->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', array_unique($websites));

        return $this;
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->_setWebsiteIds();

        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function beforeDelete()
    {
        $this->_setWebsiteIds();

        return parent::beforeDelete();
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @param $items
     * @return bool
     */
    public function validate(\Magento\Framework\DataObject $object, $items = null)
    {
        if ($items && !$this->backorderValidator->validate($this, $items)) {
            return false;
        }

        if ($object instanceof \Magento\Quote\Model\Quote\Address) {
            $object = $this->subtotalModifier->modify($object);
        }

        return parent::validate($object);
    }
}
