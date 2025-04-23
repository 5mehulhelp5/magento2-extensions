<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Block\Checkout\Onepage;

use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Framework\View\Element\Template;

class OrderTabs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var SerializerJson
     */
    private $serializer;

    /**
     * OrderTabs constructor.
     *
     * @param Template\Context $context
     * @param SerializerJson $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SerializerJson $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getJsLayout(): string
    {
        return $this->serializer->serialize($this->jsLayout);
    }
}
