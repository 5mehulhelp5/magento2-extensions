<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Asset;

interface AssetCollectorInterface
{
    public function getAssetContentType(): string;

    public function getCollectedAssets(): array;

    public function execute(string $output);
}
