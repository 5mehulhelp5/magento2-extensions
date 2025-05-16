<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\Image;

use Amasty\ImageOptimizer\Model\Image\ClearGeneratedImageForFile;

class WysiwigImage
{
    /**
     * @var ClearGeneratedImageForFile
     */
    private $clearGeneratedImageForFile;

    public function __construct(ClearGeneratedImageForFile $clearGeneratedImageForFile)
    {
        $this->clearGeneratedImageForFile = $clearGeneratedImageForFile;
    }

    /**
     * @param $subject
     * @param $target
     */
    public function beforeDeleteFile($subject, $target)
    {
        $this->clearGeneratedImageForFile->execute($target);
    }
}
