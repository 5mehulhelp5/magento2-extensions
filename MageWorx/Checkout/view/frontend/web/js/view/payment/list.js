/**
 * Copyright © MageWorx All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiComponent',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/payment/renderer-list',
    'uiLayout',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'jquery',
    'knockout'
], function (
    _,
    utils,
    Component,
    paymentMethods,
    rendererList,
    layout,
    checkoutDataResolver,
    $t,
    registry,
    quote,
    customer,
    $,
    ko
) {
    'use strict';

    function isElement(element) {
        return element instanceof Element || element instanceof HTMLDocument;
    }

    ko.virtualElements.allowedBindings.afterRender = true;
    ko.virtualElements.allowedBindings.repeat = true;

    return Component.extend({
        defaults: {
            visible: paymentMethods().length > 0,
            configDefaultGroup: {
                name: 'methodGroup',
                component: 'Magento_Checkout/js/model/payment/method-group'
            },
            paymentGroupsList: [],
            defaultGroupTitle: $t('Select a new payment method'),
            imports: {
                'activeTab': 'order_tabs.tabs:selectedTabIndex'
            },
            activeTab: 'delivery',
            defaultPaymentMethod: window.checkoutConfig.defaultPaymentMethod,
            paymentMethodSet: false,
            images: []
        },

        observableProperties: [
            'paymentGroupsList',
            'activeTab',
            'paymentMethodSet',
            'images'
        ],

        /**
         * Timer for payment method selection after changes in list / default payment method selection
         */
        selectPaymentMethodDefaultTimerId: null,

        /**
         * Initialize view.
         *
         * @returns {Component} Chainable.
         */
        initialize: function () {
            this._super()
                .initDefaultGroup()
                .initChildren();

            paymentMethods.subscribe(
                function (changes) {
                    if (customer.isLoggedIn() || quote.guestEmail) {
                        if (!this.getQuotePaymentMethodCode()) {
                            checkoutDataResolver.resolvePaymentMethod();
                        }
                    }

                    //remove renderer for "deleted" payment methods
                    _.each(changes, function (change) {
                        if (change.status === 'deleted') {
                            this.removeRenderer(change.value.method);
                            /**
                             * Reset the 'paymentMethodSet' flag in case this method has been deleted.
                             * Makes available the selection of a new method from the list.
                             */
                            if (this.getQuotePaymentMethodCode() === change.value.method) {
                                this.paymentMethodSet(false);
                            }
                        }
                    }, this);

                    //add renderer for "added" payment methods
                    _.each(changes, function (change) {
                        if (change.status === 'added') {
                            this.createRenderer(change.value);
                        }
                    }, this);

                    if (this.selectPaymentMethodDefaultTimerId) {
                        clearTimeout(this.selectPaymentMethodDefaultTimerId);
                        this.selectPaymentMethodDefaultTimerId = null;
                    }

                    this.selectPaymentMethodDefaultTimerId = setInterval(function () {
                        this.paymentMethodSet(false);
                        this.selectPaymentMethodDefault();
                        clearTimeout(this.selectPaymentMethodDefaultTimerId);
                    }.bind(this), 500);

                }, this, 'arrayChange');

            return this;
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super().observe(this.observableProperties);

            return this;
        },

        /**
         * Creates default group
         *
         * @returns {Component} Chainable.
         */
        initDefaultGroup: function () {
            layout([
                this.configDefaultGroup
            ]);

            return this;
        },

        /**
         * Create renders for child payment methods.
         *
         * @returns {Component} Chainable.
         */
        initChildren: function () {
            var self = this,
                paymentMethodsList = paymentMethods();

            _.each(paymentMethodsList, function (paymentMethodData) {
                self.createRenderer(paymentMethodData);
            });

            return this;
        },

        /**
         * Select default payment method
         *
         * @param code
         */
        selectPaymentMethodDefault: function (code) {
            let paymentMethodsList = paymentMethods();

            if (this.paymentMethodSet()) {
                return;
            }

            if (_.isEmpty(paymentMethodsList)) {
                return;
            }

            checkoutDataResolver.resolvePaymentMethod();

            if (paymentMethodsList.length === 1) {
                code = paymentMethodsList[0]['method'];
            } else if (!code) {
                code = this.defaultPaymentMethod;
            }

            if (quote.paymentMethod()) {
                code = quote.paymentMethod().method;
            }

            var paymentMethodSelector = 'index = '
                + code
                + ', displayArea = payment-methods-items-default';
            registry.async(paymentMethodSelector)(function (paymentMethod) {
                if (!this.paymentMethodSet()) {
                    paymentMethod.selectPaymentMethod();
                    this.paymentMethodSet(true);
                }
            }.bind(this));
        },

        /**
         * @returns
         */
        createComponent: function (payment) {
            var rendererTemplate,
                rendererComponent,
                templateData;

            templateData = {
                parentName: this.name,
                name: payment.name
            };
            rendererTemplate = {
                parent: '${ $.$data.parentName }',
                name: '${ $.$data.name }',
                displayArea: payment.displayArea,
                component: payment.component,
                mwImage: this.getImage(payment.method)
            };
            rendererComponent = utils.template(rendererTemplate, templateData);
            utils.extend(rendererComponent, {
                item: payment.item,
                config: payment.config
            });

            return rendererComponent;
        },

        /**
         * Create renderer.
         *
         * @param {Object} paymentMethodData
         */
        createRenderer: function (paymentMethodData) {
            var isRendererForMethod = false,
                currentGroup;

            registry.get(this.configDefaultGroup.name, function (defaultGroup) {
                _.each(rendererList(), function (renderer) {

                    if (renderer.hasOwnProperty('typeComparatorCallback') &&
                        typeof renderer.typeComparatorCallback == 'function'
                    ) {
                        isRendererForMethod = renderer.typeComparatorCallback(renderer.type, paymentMethodData.method);
                    } else {
                        isRendererForMethod = renderer.type === paymentMethodData.method;
                    }

                    if (isRendererForMethod) {
                        currentGroup = renderer.group ? renderer.group : defaultGroup;

                        this.collectPaymentGroups(currentGroup);

                        layout([
                            this.createComponent(
                                {
                                    config: renderer.config,
                                    component: renderer.component,
                                    name: renderer.type,
                                    method: paymentMethodData.method,
                                    item: paymentMethodData,
                                    displayArea: currentGroup.displayArea
                                }
                            )]);
                    }
                }.bind(this));
            }.bind(this));
        },

        /**
         * Collects unique groups of available payment methods
         *
         * @param {Object} group
         */
        collectPaymentGroups: function (group) {
            var groupsList = this.paymentGroupsList(),
                isGroupExists = _.some(groupsList, function (existsGroup) {
                    return existsGroup.alias === group.alias;
                });

            if (!isGroupExists) {
                groupsList.push(group);
                groupsList = _.sortBy(groupsList, function (existsGroup) {
                    return existsGroup.sortOrder;
                });
                this.paymentGroupsList(groupsList);
            }
        },

        /**
         * Returns payment group title
         *
         * @param {Object} group
         * @returns {String}
         */
        getGroupTitle: function (group) {
            var title = group().title;

            if (group().isDefault() && this.paymentGroupsList().length > 1) {
                title = this.defaultGroupTitle;
            }

            return title;
        },

        /**
         * Checks if at least one payment method available
         *
         * @returns {String}
         */
        isPaymentMethodsAvailable: function () {
            return _.some(this.paymentGroupsList(), function (group) {
                return this.getRegion(group.displayArea)().length;
            }, this);
        },

        /**
         * Remove view renderer.
         *
         * @param {String} paymentMethodCode
         */
        removeRenderer: function (paymentMethodCode) {
            var items;

            _.each(this.paymentGroupsList(), function (group) {
                items = this.getRegion(group.displayArea);

                _.find(items(), function (value) {
                    if (value.item.method.indexOf(paymentMethodCode) === 0) {
                        value.disposeSubscriptions();
                        value.destroy();
                    }
                });
            }, this);
        },

        /**
         * Get custom image path for payment method by code from checkout configuration.
         *
         * @param code
         * @returns {*|boolean}
         */
        getImage: function (code) {
            var images = this.images();
            if (_.isEmpty(images)) {
                return false;
            }

            if (_.isEmpty(images[code]) || _.isEmpty(images[code]['image'])) {
                return false;
            }

            return images[code]['image'];
        },

        /**
         * Get current payment method code from quote
         *
         * @returns {null|String}
         */
        getQuotePaymentMethodCode: function () {
            if (!quote || !quote.getPaymentMethod) {
                return null;
            }

            if (!_.isFunction(quote.getPaymentMethod) || !_.isFunction(quote.getPaymentMethod())) {
                return null;
            }

            if (!quote.getPaymentMethod()()) {
                return null;
            }

            return quote.getPaymentMethod()().method;
        },

        /**
         * Get payment method component from registry.
         *
         * @param code
         * @returns {*}
         */
        getPaymentMethodByCode: function (code) {
            let paymentMethodSelector = 'index = '
                + code
                + ', displayArea = payment-methods-items-default';
            return registry.get(paymentMethodSelector);
        },

        /**
         * Add custom image to payment block.
         */
        updateImage: function () {
            // Observer to add images to payment methods
            let paymentMethods = document.getElementsByClassName("payment-methods"),
                paymentMethodsContainer = paymentMethods ? paymentMethods[0] : false;

            if (!paymentMethodsContainer) {
                return;
            }

            let observer = new MutationObserver(function (mutationList, observer) {
                const self = this;

                _.each(mutationList, function (mutation) {
                    if (mutation['addedNodes'] && mutation['addedNodes'].length > 0) {
                        _.each(mutation['addedNodes'], function (addedNode) {
                            if (addedNode.classList && addedNode.classList.contains('payment-method')) {
                                let $element = $(addedNode),
                                    $paymentInput = $element.find('[name="payment[method]"]');

                                if ($paymentInput) {
                                    let paymentCode = $paymentInput.val(),
                                        paymentComponent = self.getPaymentMethodByCode(paymentCode);

                                    if (paymentComponent) {
                                        self.insertPaymentImage($element, paymentComponent);
                                    }
                                }
                            }
                        });
                    }
                });
            }.bind(this));

            observer.observe(paymentMethodsContainer, {
                    attributes: false,
                    childList: true,
                    subtree: true
                }
            );
        },

        insertPaymentImage: function (element, baseComponent) {
            let paymentMethodCode = baseComponent.index,
                imageSrc,
                imageTemplate = '<div class="payment-method__image-wrapper"><img class="payment-method__image" src="${ $.imageSrc }" alt="${ $.title } }" /></div>',
                imageData = {
                    'imageSrc': '',
                    'title': ''
                };

            if (!paymentMethodCode) {
                return;
            }

            imageSrc = baseComponent.mwImage;
            if (!imageSrc) {
                return;
            }

            imageData.imageSrc = imageSrc;
            imageData.title = baseComponent.getTitle();

            let imageDOMObject = utils.template(imageTemplate, imageData),
                $element = $(element),
                $label = $element.find('.payment-method-title .label'),
                $existingImage = $element.find('.payment-method__image');

            if ($label.length > 0) {
                if ($existingImage.length === 0) {
                    $label.prepend(imageDOMObject);
                }
            } else {
                console.log('Unable to initialize "' + paymentMethodCode + '" payment method image: unable to detect payment method label.');
            }
        }
    });
});
