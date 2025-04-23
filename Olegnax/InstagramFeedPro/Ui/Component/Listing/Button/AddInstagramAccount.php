<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Button;

use Exception;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\Instagram\Client;

class AddInstagramAccount implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var mixed|null
     */
    protected $authorization;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * AddInstagramAccount constructor.
     * @param Context $context
     * @param AuthorizationInterface $authorization
     * @param Helper $helper
     * @param Client $client
     */
    public function __construct(
        Context $context,
        AuthorizationInterface $authorization,
        Helper $helper,
        Client $client
    ) {
        $this->context = $context;
        $this->authorization = $authorization;
        $this->_helper = $helper;
        $this->client = $client;
    }

    /**
     * @return array
     * @throws Exception
     * @throws Exception
     */
    public function getButtonData(): array
    {
        if (!$this->authorization->isAllowed("Olegnax_InstagramFeedPro::IntsUser_save")) {
            return [];
        }

        $data = [
            'label' => __('Connect with Instagram'),
            'class' => 'primary',
            'type' => 'button',
            'data_attribute' => [
                'mage-init' => [
                    'Olegnax_InstagramFeedPro/js/popup' => [
                        'url' => $this->getUrl(0),
                        'windowName' => 'Instagram',
                    ],
                ],
            ],
            'on_click' => '',
        ];

        $b = $this->_helper->get();
        if ( empty($b)
            || !isset($b->data->the_key)
            || $b->data->the_key !== $this->_helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code')
            || $b->data->status !== "active"
        ) {
            $data['disabled'] = 'disabled';
            $data['data_attribute'] = [];
        }

        return $data;
    }

    /**
     * @param $store_id
     * @return string
     */
    public function getUrl($store_id): string
    {
        return $this->client->getAuthUrl($store_id);
    }
}
