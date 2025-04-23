<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Olegnax\InstagramFeedPro\Model\IntsPostFactory;

class Thumbnail extends Column
{
    /**
     * @var IntsPostFactory
     */
    protected $intsPostFactory;

    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param IntsPostFactory $intsPostFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        IntsPostFactory $intsPostFactory,
        array $components = [],
        array $data = []
    ) {
        $this->intsPostFactory = $intsPostFactory;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $_item = $this->intsPostFactory->create()->load($item['intspost_id']);
                $item['image'] =
                $item['image_src'] =
                $item['image_link'] =
                $item['image_orig_src'] = $_item->getImageUrl();
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}
