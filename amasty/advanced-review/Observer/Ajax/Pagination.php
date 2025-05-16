<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Observer\Ajax;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product;
use Magento\Review\Controller\Product as ReviewController;

class Pagination implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Product $product */
        $product = $observer->getProduct();
        /** @var ReviewController $controllerAction */
        $controllerAction = $observer->getControllerAction();
        if ($controllerAction
            && $controllerAction->getRequest()->getActionName() == 'listAjax'
            && !$controllerAction->getRequest()->isAjax()
        ) {
            $controllerAction->getResponse()->setRedirect(
                $product->getProductUrl()
            );
        }
    }
}
