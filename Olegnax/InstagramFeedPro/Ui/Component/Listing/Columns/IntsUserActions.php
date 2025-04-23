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
use Olegnax\InstagramFeedPro\Model\Data\IntsUser;

class IntsUserActions extends Column
{

    const URL_PATH_DELETE = 'olegnax_instagramfeedpro/intsuser/delete';
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
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = [];
        $content[] = '<a class="action-menu-item" target="_self" href="' .
            $this->urlBuilder->getUrl(
                static::URL_PATH_DELETE,
                [
                    IntsUser::INTSUSER_ID => $item[IntsUser::INTSUSER_ID],
                ]
            ) . '" data-action="item-update">' . __('Delete') . '</a>';
        return implode(
                '<br/>',
                $content
            ) .
            '<script>document.body.dispatchEvent(new Event("contentUpdated"));</script>';
    }
}
