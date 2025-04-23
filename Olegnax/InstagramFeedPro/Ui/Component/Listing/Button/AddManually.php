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
use Magento\Framework\UrlInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Olegnax\InstagramFeedPro\Helper\Helper;

class AddManually implements ButtonProviderInterface
{
    const URL_PATH_ADD = 'olegnax_instagramfeedpro/intsuser/add';
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var mixed|null
     */
    protected $authorization;
    /**
     * @var Helper
     */
    protected $_helper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * AddManually constructor.
     * @param Context $context
     * @param AuthorizationInterface $authorization
     * @param Helper $helper
     */
    public function __construct(
        Context $context,
        AuthorizationInterface $authorization,
        Helper $helper,
        UrlInterface $urlBuilder
    ) {
        $this->context = $context;
        $this->authorization = $authorization;
        $this->_helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed("Olegnax_InstagramFeedPro::IntsUser_add")) {
            return [];
        }

        $data = [
            'label' => __('Add Manually'),
            'class' => 'primary ox-inst-modal__add-user-trigger disabled',
            'type' => 'button',
            'data_attribute' => [
                'mage-init' => [
                    'Olegnax_InstagramFeedPro/js/adduser' => [
                        'url' => $this->getUrl(0)
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
    public function getUrl($store_id)
    {
        return $this->urlBuilder->getUrl(static::URL_PATH_ADD, ['store_id' => $store_id]);
    }
}
