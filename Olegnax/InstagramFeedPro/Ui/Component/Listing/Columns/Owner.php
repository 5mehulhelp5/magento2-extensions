<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Olegnax\InstagramFeedPro\Model\Config\Source\Users as Collection;

class Owner extends Column
{

    /**
     * @var string[]
     */
    protected $items;

    /**
     * Owner constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Collection $collection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Collection $collection,
        array $components = [],
        array $data = []
    ) {
        $this->items = $collection->toArray();
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['owner'] = $this->getItem($item['owner']);
            }
        }

        return parent::prepareDataSource($dataSource);
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function getItem($identifier)
    {
        return array_key_exists($identifier, $this->items) ? $this->items[$identifier] : $identifier;
    }
}
