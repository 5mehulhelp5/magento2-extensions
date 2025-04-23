<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Plugin\Catalog\Product\View;

use Amasty\Label\Model\LabelViewer;
use Amasty\Label\Model\ResourceModel\Label\Collection;

class Label
{
    /**
     * @var array
     */
    private $allowedNames = [
        'product.info.media.magiczoomplus',
        'product.info.media.image',
        'product.info.media.magicthumb.younify'
    ];

    /**
     * @var LabelViewer
     */
    private $helper;

    public function __construct(
        LabelViewer $helper,
        array $allowedNames = []
    ) {
        $this->helper = $helper;
        $this->allowedNames = array_merge($this->allowedNames, array_values($allowedNames));
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        $product = $subject->getProduct();
        $name = $subject->getNameInLayout();

        if ($product
            && in_array($name, $this->getAllowedNames())
            && !$subject->getAmlabelObserved()
        ) {
            $subject->setAmlabelObserved(true);
            $result .= $this->helper->renderProductLabel($product, Collection::MODE_PDP);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedNames()
    {
        return $this->allowedNames;
    }
}
