<?php

namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Framework\Notification\MessageInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;

class Message implements MessageInterface
{
    const MESSAGE_IDENTITY = 'ox_instagram_feed_pro';

    protected $_text;
    protected $_helper;

    public function __construct(Helper $helper)
    {
        $this->_helper = $helper;
        $license = $helper->get();
        if ($this->_helper->isEnabled()
            && !empty($license)
            && isset($license->data->the_key)
            && $license->data->the_key == $helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code')
            && $license->data->status == "active"
        ) {
            try {
                if (!$this->_helper->validate()) {
                    throw new Exception('License validate failed');
                }
            } catch (Exception $e) {
                $this->_text = sprintf(__('Olegnax Instagram Pro: %s'), $e->getMessage());
            }
        }
    }

    public function getIdentity()
    {
        return static::MESSAGE_IDENTITY;
    }

    public function getSeverity()
    {
        return MessageInterface::SEVERITY_CRITICAL;
    }

    public function isDisplayed()
    {
        $text = $this->getText();
        return !empty($text);
    }

    public function getText()
    {
        return $this->_text;
    }

}