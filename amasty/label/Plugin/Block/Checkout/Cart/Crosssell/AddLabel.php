<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\Block\Checkout\Cart\Crosssell;

use Amasty\Label\Model\LabelViewer;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Magento\Framework\Registry;
use Magento\TargetRule\Block\Checkout\Cart\Crosssell;
use Magento\Checkout\Block\Cart\Crosssell as CheckoutCrosssell;

class AddLabel
{
    /**
     * @var LabelViewer
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        LabelViewer $helper,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @param CheckoutCrosssell|Crosssell $subject
     * @param string $result
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        $subject,
        string $result
    ): string {
        if (!$this->registry->registry('amlabel_category_observer') && !$subject->getIsAmLabelObserved()) {
            $products = $subject->getItemCollection();
            if (!$products) {
                $products = $subject->getItems();
            }

            if ($products) {
                foreach ($products as $product) {
                    $result .= $this->helper->renderProductLabel(
                        $product,
                        Collection::MODE_LIST,
                        true
                    );
                }

                $subject->setIsAmLabelObserved(true);
            }
        }

        return $result;
    }
}
