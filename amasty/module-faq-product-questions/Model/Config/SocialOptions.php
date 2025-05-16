<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Config;

use Amasty\Faq\Model\SocialDataList;
use Magento\Framework\Data\OptionSourceInterface;

class SocialOptions implements OptionSourceInterface
{
    /**
     * @var SocialDataList
     */
    private $socialDataList;

    public function __construct(
        SocialDataList $socialDataList
    ) {
        $this->socialDataList = $socialDataList;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->socialDataList->getSocialList() as $socialData) {
            $options[] = ['value' => $socialData->getCode(), 'label'=> __($socialData->getName())];
        }

        return $options;
    }
}
