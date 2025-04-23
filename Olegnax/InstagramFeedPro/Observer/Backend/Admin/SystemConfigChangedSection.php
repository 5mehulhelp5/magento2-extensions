<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\InstagramFeedPro\Observer\Backend\Admin;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Olegnax\InstagramFeedPro\Model\DynamicStyle\Generator;

class SystemConfigChangedSection implements ObserverInterface
{
    /**
     * @var Generator
     */
    protected $_generator;

    /**
     * SystemConfigChangedSection constructor.
     * @param Generator $generator
     */
    public function __construct(
        Generator $generator
    ) {
        $this->_generator = $generator;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(
        Observer $observer
    ) {
        $this->_generator->generate($observer->getData('website'), $observer->getData('store'));
    }
}
