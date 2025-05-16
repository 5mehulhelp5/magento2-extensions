<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\ViewModel\Reviews\Product\View\ListView;

use Amasty\AdvancedReview\Helper\BlockHelper;
use Amasty\AdvancedReview\Model\Frontend\Review\IsAllowWriteReview;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class WriteReviewButton implements ArgumentInterface
{
    /**
     * @var IsAllowWriteReview
     */
    private $isAllowWriteReview;

    public function __construct(
        ?BlockHelper $blockHelper, // @deprecated
        ?IsAllowWriteReview $isAllowWriteReview = null
    ) {
        $this->isAllowWriteReview = $isAllowWriteReview ?? ObjectManager::getInstance()->get(IsAllowWriteReview::class);
    }

    public function isCanRender(): bool
    {
        return $this->isAllowWriteReview->execute();
    }

    public function getButtonUrl(): string
    {
        return '#review-form';
    }
}
