<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    public const XML_PATH_WHO_CAN_SUBMIT = 'general/who_can_submit';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_advancedreview/';

    public function getWhoCanSubmit(?int $storeId = null): string
    {
        return (string)$this->getValue(self::XML_PATH_WHO_CAN_SUBMIT, $storeId);
    }
}
