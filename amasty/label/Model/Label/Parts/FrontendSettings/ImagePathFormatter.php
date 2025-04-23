<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Parts\FrontendSettings;

/**
 * Brings image paths loaded from the media gallery and locally into the same format
 */
class ImagePathFormatter
{
    public const AMASTY_LABEL_MEDIA_PATH = 'amasty/amlabel';
    public const AMASTY_LABEL_TMP_MEDIA_PATH = 'amasty/tmp/amlabel';
    public const MEDIA_PATH = '/media/';

    /**
     * @param string|null $imageName
     * @return string
     */
    public function execute(?string $imageName): string
    {
        $isBeginsWithMediaDirectoryPath = strpos($imageName, self::MEDIA_PATH) === 0;
        
        return $isBeginsWithMediaDirectoryPath
            ? substr($imageName, strlen(self::MEDIA_PATH))
            : sprintf('%s/%s', self::AMASTY_LABEL_MEDIA_PATH, $imageName);
    }
}
