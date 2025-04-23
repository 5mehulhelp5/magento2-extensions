<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Olegnax\InstagramFeedPro\Model\Data\IntsPost;

class IntsPostActions extends Column
{

    const URL_PATH_EDIT = 'olegnax_instagramfeedpro/intspost/edit';
    const URL_PATH_DELETE = 'olegnax_instagramfeedpro/intspost/delete';
    const URL_PATH_DETAILS = 'olegnax_instagramfeedpro/intspost/details';
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[IntsPost::INTSPOST_ID])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    IntsPost::INTSPOST_ID => $item[IntsPost::INTSPOST_ID],
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    IntsPost::INTSPOST_ID => $item[IntsPost::INTSPOST_ID],
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Post'),
                                'message' => __(
                                    'Are you sure you want to delete this Post?'
                                ),
                            ],
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
