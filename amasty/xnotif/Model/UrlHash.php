<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model;

/**
 * @deprecated
 * @see \Amasty\Xnotif\Model\Notification\DefaultAlert\Url\UnsubscribeParameterProcessor
 */
class UrlHash
{
    public const SALT = 'qprugn1234njd';

    /**
     * @param int $productId
     * @param string $email
     *
     * @return string
     */
    public function getHash($productId, $email)
    {
        return hash('sha256', $productId . $email . self::SALT);
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @return bool
     */
    public function check(\Magento\Framework\App\Request\Http $request)
    {
        $hash = urldecode((string)$request->getParam('hash'));
        $productId = $request->getParam('product_id');
        $email = urldecode((string)$request->getParam('email'));

        if (empty($hash) || empty($productId) || empty($email)) {
            return false;
        }

        $real = $this->getHash($productId, $email);

        return $hash === $real;
    }
}
