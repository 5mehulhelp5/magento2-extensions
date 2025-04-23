<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Ui\Component\Listing\Columns\Link;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\OptionSource\Status as StatusOptions;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Status extends Column
{
    /**
     * @var UrlKey
     */
    private $urlKey;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlKey $urlKey,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlKey = $urlKey;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!in_array($item[ItemInterface::LINK_TYPE], $this->urlKey->getValues())) {
                    $item[ItemInterface::STATUS] = StatusOptions::DISABLED;
                }
            }
        }

        return $dataSource;
    }
}
