<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Rich Snippets for Magento 2
 */

namespace Amasty\SeoRichData\Model\JsonLd\Processor;

use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class SocialProfiles implements ProcessorInterface
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    public function process(array $data): array
    {
        if ($this->configHelper->forSocialEnabled() && $this->configHelper->forOrganizationEnabled()) {
            foreach ($this->configHelper->getSocialLinks() as $socialLink) {
                $data['organization']['sameAs'][] = $socialLink;
            }
        }

        return $data;
    }
}
