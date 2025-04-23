<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;

class UserThumbnail extends Column
{
    protected $storeManager;
    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     * @noinspection PhpDeprecationInspection
     */
    public function prepareDataSource(array $dataSource)
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['image'] =
                $item['image_src'] =
                $item['image_link'] =
                $item['image_orig_src'] = $mediaUrl . $item['profile_picture'];
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
