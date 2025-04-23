<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\InstagramFeedPro\Block;


use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class StyleBlock extends Template
{
    /**
     * @var Json|mixed|null
     */
    protected $json;

    public function __construct(
        Template\Context $context,
        array $data = [],
        Json $json = null
    ) {
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $data);
    }

    public function getCacheKeyInfo()
    {
        return array_replace(parent::getCacheKeyInfo(), [
            'name' => $this->getNameInLayout(),
            'data' => $this->json->serialize($this->getData()),
        ]);
    }
}
