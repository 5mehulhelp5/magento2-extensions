<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Ui\DataProvider\Form\Link\Modifier;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenuLite\Model\OptionSource\Status;
use Amasty\MegaMenuLite\Model\OptionSource\UrlKey;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class ModifyStatus implements ModifierInterface
{
    /**
     * @var UrlKey
     */
    private $urlKey;

    public function __construct(UrlKey $urlKey)
    {
        $this->urlKey = $urlKey;
    }

    public function modifyData(array $data)
    {
        foreach ($data as $key => $linkData) {
            if (!in_array($linkData[ItemInterface::LINK_TYPE], $this->urlKey->getValues())) {
                $data[$key][ItemInterface::STATUS] = Status::DISABLED;
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
