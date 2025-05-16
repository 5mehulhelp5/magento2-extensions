<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Plugins\Bundle\Block\Catalog\Product\View\Type;

use Amasty\Xnotif\Helper\Data;
use Amasty\Xnotif\Plugins\Bundle\Block\Catalog\Product\View\Type\Bundle\ShowOutOfStock;
use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle as BundleSubject;
use Magento\Framework\Serialize\Serializer\Json;

class Bundle
{
    public const OPTIONS_NAME = 'product.info.bundle.options';

    /**
     * @var Json
     */
    private $jsonEncoder;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Json $jsonEncoder,
        Data $helper
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->helper = $helper;
    }

    /**
     * @param BundleSubject $subject
     * @param string $html
     * @return string
     */
    public function afterToHtml(BundleSubject $subject, string $html): string
    {
        if ($subject->getNameInLayout() === self::OPTIONS_NAME) {
            $json = [];
            foreach ($subject->getOptions() as $option) {
                foreach ($option->getSelections() as $selection) {
                    /*generate information only for out of stock items*/
                    if (!$selection->getData(ShowOutOfStock::NATIVE_STOCK_STATUS)) {
                        $json[$selection->getId()] = [
                            'is_salable' => (int) $selection->getData(ShowOutOfStock::NATIVE_STOCK_STATUS),
                            'alert' => $this->helper->getStockAlert($selection)
                        ];
                    }
                }
            }

            $json = $this->jsonEncoder->serialize($json);
            $html = sprintf('<script>window.amxnotif_json_config = %s</script>%s', $json, $html);
        }

        return $html;
    }
}
