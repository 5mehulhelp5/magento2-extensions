<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Setup\Patch\Data;

use Amasty\AdvancedReview\Model\ResourceModel\Review\ApplyVerifyBadge as ApplyVerifyBadgeResource;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ApplyVerifyBadge implements DataPatchInterface
{
    /**
     * @var ApplyVerifyBadgeResource
     */
    private $applyVerifyBadge;

    public function __construct(
        ApplyVerifyBadgeResource $applyVerifyBadge
    ) {
        $this->applyVerifyBadge = $applyVerifyBadge;
    }

    /**
     * @inheirtDoc
     */
    public function apply()
    {
        if ($this->applyVerifyBadge->isApplicable()) {
            $this->applyVerifyBadge->execute();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
