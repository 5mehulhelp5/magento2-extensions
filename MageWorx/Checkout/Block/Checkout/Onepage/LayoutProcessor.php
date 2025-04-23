<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Checkout\Onepage;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleList;
use MageWorx\Checkout\Api\LayoutModifierAccessInterface;
use MageWorx\Checkout\Api\CartManagerInterface;

/**
 * Class LayoutProcessor
 *
 * Main layout processor for checkout.
 * Rearrange components, update default data.
 */
class LayoutProcessor implements LayoutProcessorInterface, LayoutModifierAccessInterface
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    private $attributeMapper;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    private $attributeMerger;

    /**
     * @var \Magento\Customer\Model\Options
     */
    private $options;

    /**
     * @var array
     */
    private $addressFieldPairs;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \MageWorx\Checkout\Api\CheckoutConfigInterface
     */
    private $checkoutConfig;

    /**
     * @var array
     */
    private $jsLayout;

    /**
     * @var CartManagerInterface
     */
    private $cartManager;

    /**
     * @var ModuleList
     */
    protected $moduleList;

    /**
     * LayoutProcessor constructor.
     *
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $attributeMerger
     * @param \Magento\Customer\Model\Options $options
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \MageWorx\Checkout\Api\CheckoutConfigInterface $checkoutConfig
     * @param CartManagerInterface $cartManager
     * @param array $addressFieldPairs
     */
    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Magento\Checkout\Block\Checkout\AttributeMerger $attributeMerger,
        \Magento\Customer\Model\Options $options,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Request\Http $request,
        \MageWorx\Checkout\Api\CheckoutConfigInterface $checkoutConfig,
        CartManagerInterface $cartManager,
        ModuleList $moduleList,
        array $addressFieldPairs = []
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper               = $attributeMapper;
        $this->attributeMerger               = $attributeMerger;
        $this->options                       = $options;
        $this->eventManager                  = $eventManager;
        $this->request                       = $request;
        $this->checkoutConfig                = $checkoutConfig;
        $this->cartManager                   = $cartManager;
        $this->addressFieldPairs             = $addressFieldPairs;
        $this->moduleList                    = $moduleList;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @throws LocalizedException
     */
    public function process($jsLayout): array
    {
        if (!$this->isApplicable()) {
            return $jsLayout;
        }

        $this->setJsLayout($jsLayout);

        $this->eventManager->dispatch(
            'mageworx_checkout_js_layout_start_processing',
            [
                'subject' => $this
            ]
        );

        $attributesToConvert = [
            'prefix' => [$this->options, 'getNamePrefixOptions'],
            'suffix' => [$this->options, 'getNameSuffixOptions'],
        ];

        $elements = $this->getAddressAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);

        $this->moveBillingAddressList($this->jsLayout);

        $this->jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['billingAddress']
        ['children']['billing-address-fieldset']['children']['shared'] =
            $this->getBillingAddressComponent($elements);

        $shippingAddressFields = &$this->jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
//        $this->groupAddressFields($shippingAddressFields);

        // Pick Up In Store tab > Address Fieldset
        $this->addShippingAddressFieldsToPickupTab($shippingAddressFields);

        $billingAddressFields = &$this->jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['billingAddress']['children']['billing-address-fieldset']['children']['shared']
        ['children']['form-fields']['children'];
        $this->groupAddressFields($billingAddressFields);

        $this->moveDiscountCodeInput($this->jsLayout);
        $this->moveGiftCardsInput($this->jsLayout);
        $this->moveOriginalCheckoutAgreements($this->jsLayout);
        $this->processOrderComments($this->jsLayout);
        $this->processPaymentMethods($this->jsLayout);
        $this->processEmailSubscriptionCheckbox($this->jsLayout);
        $this->moveCustomerBalanceComponents($this->jsLayout);

        if ($this->cartManager->getCurrentCart() && $this->cartManager->getCurrentCart()->getIsVirtual()) {
            $this->addEmailToBillingAddress($this->jsLayout);
        }

        if ($this->isModuleEnabled('Amazon_Payment')) {
            $paymentConfig = &$this->jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment'];
            $paymentConfig['children']['payments-list']['component'] = 'MageWorx_Checkout/js/view/payment/amazon-payment-list';
        }

        $this->eventManager->dispatch(
            'mageworx_checkout_js_layout_end_processing',
            [
                'subject' => $this
            ]
        );

        return $this->getJsLayout();
    }

    /**
     * Check if module is enabled
     *
     * @param $moduleName
     * @return array
     */
    protected function isModuleEnabled($moduleName)
    {
        return $this->moduleList->getOne($moduleName);
    }

    /**
     * Adds email input to the billing address for virtual quotes
     *
     * @param array $jsLayout
     */
    private function addEmailToBillingAddress(array &$jsLayout): void
    {
        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['billingAddress']
        )) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['billingAddress']['children']['customer-email'] = [
                'component' => 'MageWorx_Checkout/js/view/form/element/email',
                'namespace' => 'billing-form',
                'displayArea' => 'customer-email',
            ];
        }
    }

    /**
     * @param array $shippingAddressFields
     * @return array
     */
    private function addShippingAddressFieldsToPickupTab(array $shippingAddressFields): array
    {
        if (isset(
            $this->jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['pickupInformation']
        )) {
            unset($shippingAddressFields['street']);
            unset($shippingAddressFields['country_id-region-region_id-group']);
            unset($shippingAddressFields['city-postcode-group']);
            unset($shippingAddressFields['fax']);
            if (isset(
                $shippingAddressFields['company-telephone-group']['children']['field-group']['children']['telephone']
            )) {
                $telephone = $shippingAddressFields['company-telephone-group']['children']['field-group']
                ['children']['telephone'];

                $telephone['validation'] = [
                    'required-entry'    => true
                ];

                $shippingAddressFields['telephone'] = $telephone;
                unset($shippingAddressFields['company-telephone-group']);
            }
            $this->jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['pickupInformation']['children']['shipping-address-fieldset']['children'] =
                $shippingAddressFields;
        }

        return $this->jsLayout;
    }

    /**
     * Input point to elements modifications
     *
     * @param string $code
     * @param array $element
     * @return array
     */
    public function preprocessElement(string $code, array &$element): array
    {
        $this->eventManager->dispatch(
            'preprocess_checkout_element',
            [
                'element_name' => $code,
                'element'      => $element
            ]
        );

        return $element;
    }

    /**
     * Return actual js layout (link)
     *
     * @return array
     */
    public function &getJsLayout(): array
    {
        return $this->jsLayout;
    }

    /**
     * Set actual js layout
     *
     * @param array $jsLayout
     * @return LayoutProcessor
     */
    private function setJsLayout(array &$jsLayout): LayoutProcessor
    {
        $this->jsLayout = $jsLayout;

        return $this;
    }

    /**
     * Move customer balance components (defined in the CustomerBalance module) to the "before grand total" area
     *
     * @param array $jsLayout
     */
    private function moveCustomerBalanceComponents(array &$jsLayout): void
    {
        $code = 'storeCredit';

        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children'][$code]
        )) {
            // Saving customer balance element
            /** @var array $customerBalanceElement */
            $customerBalanceElement = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children'][$code];

            // Remove original customer balance element
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children'][$code]
            );

            $customerBalanceElement['component'] =
                'MageWorx_Checkout/js/view/summary/additional-inputs/customer-balance';
            $customerBalanceElement['template']  =
                'MageWorx_Checkout/summary/additional-inputs/customer-balance';

            $customerBalanceElement['config']['label']               = __('Store Credit');
            $customerBalanceElement['config']['storeCreditFormName'] = 'checkout.sidebar.additionalInputs.' . $code;

            $customerBalanceElement = $this->preprocessElement($code, $customerBalanceElement);

            $customerBalanceTotalsTemplate = 'MageWorx_Checkout/summary/totals/customer-balance';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
            ['children']['customerbalance']['config']['template'] = $customerBalanceTotalsTemplate;
            $customerBalanceTotalsComponent = 'MageWorx_Checkout/js/view/summary/totals/customer-balance';
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
            ['children']['customerbalance']['component'] = $customerBalanceTotalsComponent;

            $jsLayout['components']['checkout']['children']['sidebar']['children']['additionalInputs']['children']
            [$code] = $customerBalanceElement;
        }
    }

    /**
     * Move discount code input element (defined in the SalesRule module) to appear after the order total
     *
     * @param array $jsLayout
     */
    private function moveDiscountCodeInput(array &$jsLayout): void
    {
        $code = 'discount';

        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children'][$code]
        )) {
            // Saving discount code element
            $discountCodeElement = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children'][$code];

            // Remove original discount code element
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children'][$code]
            );

            // Use the existing MageWorx discount component but move it after the order total
            $discountCodeElement['component'] = 'MageWorx_Checkout/js/view/summary/additional-inputs/discount';
            $discountCodeElement['template']  = 'MageWorx_Checkout/summary/additional-inputs/discount';
            $discountCodeElement['displayArea'] = 'after_summary';
            $discountCodeElement['sortOrder'] = 70; // Higher sort order to appear after grand total

            $discountCodeElement['config']['tooltipMessage'] = $this->checkoutConfig->getCouponCodeTooltip();

            $discountCodeElement = $this->preprocessElement($code, $discountCodeElement);

            if ($this->checkoutConfig->isCouponCodeBlockVisible()) {
                // Add directly to sidebar instead of additionalInputs to control placement
                $jsLayout['components']['checkout']['children']['sidebar']['children'][$code] = $discountCodeElement;
            }
        }
    }

    /**
     * Move magento gift card input element (defined in the GiftCardAccount module) to the "before grand total" area
     *
     * @param array $jsLayout
     */
    private function moveGiftCardsInput(array &$jsLayout): void
    {
        $code = 'giftCardAccount';

        if (isset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['afterMethods']['children'][$code]
        )) {
            // Saving original element
            $originalElement = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']['afterMethods']['children'][$code];

            // Remove original element
            unset(
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['afterMethods']['children'][$code]
            );

            $originalElement['component'] = 'MageWorx_Checkout/js/view/summary/additional-inputs/magento-gift-card';
            $originalElement['template']  = 'MageWorx_Checkout/summary/additional-inputs/magento-gift-card';

            $originalElement = $this->preprocessElement($code, $originalElement);

            $jsLayout['components']['checkout']['children']['sidebar']['children']['additionalInputs']
            ['children'][$code] = $originalElement;
        }
    }

    /**
     * Process order comments component
     *
     * @param array $jsLayout
     */
    private function processOrderComments(array &$jsLayout): void
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['order_comment']['config']['label']   = $this->checkoutConfig->getOrderCommentsLabel();
        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['order_comment']['config']['visible'] = $this->checkoutConfig->isOrderCommentsEnabled();
    }

    /**
     * Add images to payment methods
     *
     * @param array $jsLayout
     */
    private function processPaymentMethods(array &$jsLayout): void
    {
        if ($this->checkoutConfig->isDisplayPaymentMethodImage()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
            ['children']['payments-list']['config']['images'] = $this->checkoutConfig->getPaymentMethodsImages();
        }

        if (!empty($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['payment']['children']['payments-list']['children'])) {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as &$child) {
                if ($child['component'] === 'Magento_Checkout/js/view/billing-address') {
                    $child['detailsTemplate'] = 'MageWorx_Checkout/payment-method/billing-address/details';
                }
            }
        }
    }

    /**
     * Remove email subscription checkbox in case it is disabled in store config
     *
     * @param array $jsLayout
     */
    private function processEmailSubscriptionCheckbox(array &$jsLayout): void
    {
        if (!$this->checkoutConfig->isEmailSubscriptionEnabled()) {
            unset($jsLayout['components']['checkout']['children']['sidebar']['children']['newsletter-subscription']);

            return;
        }

        $jsLayout['components']['checkout']['children']['sidebar']['children']['newsletter-subscription']
        ['config']['label'] = $this->checkoutConfig->getEmailSubscriptionTitle();
    }

    /**
     * Move original checkout agreements block to the Terms and Conditions section
     *
     * @param array $jsLayout
     */
    private function moveOriginalCheckoutAgreements(array &$jsLayout): void
    {
        $agreements = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment']['children']['payments-list']['children']['before-place-order']
        ['children']['agreements'];
        unset(
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
            ['children']['payment']['children']['payments-list']['children']['before-place-order']
            ['children']['agreements']
        );

        // Move to Terms and Conditions section in the main column
        $agreements['displayArea'] = 'terms';
        $agreements['template']    = 'MageWorx_Checkout/checkout-agreements';
        $agreements['component']   = 'Magento_CheckoutAgreements/js/view/checkout-agreements';
        $agreements['config']['agreementsMessage'] = $this->checkoutConfig->getCheckoutAgreementsMessage();

        // Add to the Terms and Conditions section
        if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['terms'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['terms'] = [
                'component' => 'uiComponent',
                'displayArea' => 'terms',
                'children' => []
            ];
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['terms']['children']['agreements'] = $agreements;
    }

    /**
     * Group some fields inside address form to one row
     *
     * @param array $addressFieldset
     */
    private function groupAddressFields(array &$addressFieldset): void
    {
        $fieldsPairsConfig = $this->addressFieldPairs;
        foreach ($fieldsPairsConfig as $index => $config) {
            $pairedFields = [];
            $pair         = $config['fields'] ?? [];
            $sortOrder    = $config['sortOrder'] ?? '';
            foreach ($pair as $field) {
                // Add required class name to display pair in one row
                if (isset($addressFieldset[$field]['additionalClasses'])) {
                    if (is_array($addressFieldset[$field]['additionalClasses'])) {
                        $addressFieldset[$field]['additionalClasses']['form__field'] = true;
                    } elseif (is_string($addressFieldset[$field]['additionalClasses'])) {
                        $addressFieldset[$field]['additionalClasses'] .= ' form__field';
                    }
                } else {
                    $addressFieldset[$field]['additionalClasses'] = 'form__field';
                }

                // Add container with updated fields
                $pairedFields[$field] = $addressFieldset[$field];
                // Remove original field
                unset($addressFieldset[$field]);
            }

            $addressFieldset[$index . '-group'] = [
                'component' => 'uiComponent',
                'template'  => 'MageWorx_Checkout/form/row',
                'sortOrder' => $sortOrder,
                'children'  => [
                    'field-group' => [
                        'component'   => 'uiComponent',
                        'displayArea' => 'field-group',
                        'children'    => $pairedFields
                    ]
                ]
            ];
        }
    }

    /**
     * @param array $jsLayout
     */
    private function moveBillingAddressList(array &$jsLayout): void
    {
        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['billingAddress']['billingAddressListProvider'] = '${$.name}.billingAddressList';

        $billingAddressList = [
            'component'   => 'MageWorx_Checkout/js/view/billing-address/list',
            'displayArea' => 'billing-address-list',
            'template'    => 'MageWorx_Checkout/billing-address/list'
        ];
        $billingAddressList = $this->preprocessElement('billingAddressList', $billingAddressList);

        $jsLayout['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['billingAddress']['children']['billingAddressList'] = $billingAddressList;
    }

    /**
     * Gets billing address component details
     *
     * @param array $elements
     * @return array
     */
    private function getBillingAddressComponent($elements): array
    {
        return [
            'component'       => 'MageWorx_Checkout/js/view/billing-address',
            'displayArea'     => 'billing-address-form',
            'provider'        => 'checkoutProvider',
            'deps'            => 'checkoutProvider',
            'dataScopePrefix' => 'billingAddress',
            'children'        => [
                'form-fields' => [
                    'component'   => 'uiComponent',
                    'displayArea' => 'additional-fieldsets',
                    'children'    => $this->attributeMerger->merge(
                        $elements,
                        'checkoutProvider',
                        'billingAddress',
                        [
                            'country_id' => [
                                'sortOrder' => 70,
                            ],
                            'region'     => [
                                'visible' => false,
                            ],
                            'region_id'  => [
                                'component'  => 'Magento_Ui/js/form/element/region',
                                'config'     => [
                                    'template'    => 'ui/form/field',
                                    'elementTmpl' => 'ui/form/element/select',
                                    'customEntry' => 'billingAddress.region',
                                ],
                                'validation' => [
                                    'required-entry' => true,
                                ],
                                'filterBy'   => [
                                    'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                    'field'  => 'country_id',
                                ],
                            ],
                            'postcode'   => [
                                'component'  => 'Magento_Ui/js/form/element/post-code',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                            'company'    => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'fax'        => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'telephone'  => [
                                'config' => [
                                    'tooltip' => [
                                        'description' => __('For delivery questions.'),
                                    ],
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    /**
     * Is that layout applicable
     *
     * @return bool
     */
    private function isApplicable(): bool
    {
        if (!$this->checkoutConfig->isEnabled()) {
            // Disabled in store configuration by admin
            return false;
        }

        $route = $this->request->getRouteName();
        if ($route !== 'mageworx_checkout') {
            // Do not change anything outside our route
            return false;
        }

        return true;
    }

    /**
     * Get address attributes.
     *
     * @return array
     * @throws LocalizedException
     */
    private function getAddressAttributes(): array
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label                    = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }

        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    private function convertElementsToSelect(array $elements, array $attributesToConvert): array
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }

            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType']    = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $elements;
    }
}
