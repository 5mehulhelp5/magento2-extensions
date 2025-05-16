<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\ViewModel\Summary;

use Amasty\AdvancedReview\Block\Summary;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class SummaryRenderer implements SummaryRendererInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var string
     */
    private $template;

    public function __construct(
        BlockFactory $blockFactory,
        ?string $template = null
    ) {
        $this->blockFactory = $blockFactory;
        $this->template = $template;
    }

    public function render(ReviewCollection $collection, Product $product): string
    {
        /** @var Summary $block **/
        $block = $this->blockFactory->createBlock(Summary::class);
        $block->setProduct($product);
        $block->setDisplayedCollection($collection);

        if ($this->template !== null) {
            $block->setTemplate($this->template);
        }

        return $block->toHtml();
    }
}
