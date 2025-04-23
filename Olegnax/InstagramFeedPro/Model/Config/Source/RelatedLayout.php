<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\View\Asset\Repository;

class RelatedLayout implements ArrayInterface
{
    const V1 = 'list';
    const V2 = 'grid';
    const V3 = 'grid -v2';
    protected $_assetRepo;

    /**
     * RelatedLayout constructor.
     * @param Repository $assetRepo
     */
    public function __construct(
        Repository $assetRepo
    ) {
        $this->_assetRepo = $assetRepo;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $array = $this->toArray();
        foreach ($array as $key => $value) {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        return [
            static::V2 => $this->_assetRepo->getUrl('Olegnax_InstagramFeedPro::images/related-layout-grid.jpg'),
            static::V3 => $this->_assetRepo->getUrl('Olegnax_InstagramFeedPro::images/related-layout-grid2.jpg'),
			static::V1 => $this->_assetRepo->getUrl('Olegnax_InstagramFeedPro::images/related-layout-list.jpg'),
        ];
    }
}
