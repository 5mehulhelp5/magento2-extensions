<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\ProductAlert\Block\Email;

use Amasty\Xnotif\Model\Notification\DefaultAlert\CustomerRegistry;
use Amasty\Xnotif\Model\Notification\DefaultAlert\Url\UnsubscribeParameterProcessor;

class Url
{
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var UnsubscribeParameterProcessor
     */
    private $unsubscribeParameterProcessor;

    /**
     * @var int
     */
    private $productId;

    public function __construct(
        CustomerRegistry $customerRegistry,
        UnsubscribeParameterProcessor $unsubscribeParameterProcessor
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->unsubscribeParameterProcessor = $unsubscribeParameterProcessor;
    }

    /**
     * @param $subject
     * @return null|string
     */
    private function getType($subject)
    {
        $type = null;
        if ($subject instanceof \Magento\ProductAlert\Block\Email\Price) {
            $type = 'price';
        }
        if ($subject instanceof \Magento\ProductAlert\Block\Email\Stock) {
            $type = 'stock';
        }

        return $type;
    }

    /**
     * @param $subject
     * @param string $route
     * @param array $params
     * @return array
     */
    public function beforeGetUrl($subject, $route = '', $params = [])
    {
        if ($customer = $this->customerRegistry->get()) {
            $type = $this->getType($subject);
            if ($type) {
                $isGuest = !$customer->getId();
                $hash = $this->unsubscribeParameterProcessor->encode(
                    $isGuest ? 'email' : 'customer_id',
                    $isGuest ? $customer->getEmail() : (string)$customer->getId()
                );

                $params['product_id'] = $this->getProductId();
                $params['hash'] = $hash;
                $params['type'] = $type;
            }
        }

        return [$route, $params];
    }

    /**
     * @param $subject
     * @param $productId
     */
    public function beforeGetProductUnsubscribeUrl($subject, $productId)
    {
        $this->setProductId($productId);
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }
}
