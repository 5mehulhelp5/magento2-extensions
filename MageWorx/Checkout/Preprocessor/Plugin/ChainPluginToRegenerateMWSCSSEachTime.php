<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Preprocessor\Plugin;

use Magento\Framework\View\Asset\PreProcessor\Chain;

/**
 * Class ChainPluginToRegenerateMWSCSSEachTime
 *
 * Set flag isChanged to true each time when mwscss files checked.
 * The view_preprocessed cached files will be ignored for mwscss files.
 */
class ChainPluginToRegenerateMWSCSSEachTime
{
    public function afterIsChanged(Chain $subject, $result)
    {
        if (PHP_SAPI !== 'cli') {
            return $result;
        }

        $asset = $subject->getAsset();
        if ($asset->getModule() === 'MageWorx_Checkout'
            && $asset->getFilePath() === 'css/main.less'
        ) {
            return true;
        }

        return $result;
    }
}
