<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Model\Integration\Shopby;

class GetConfigProvider
{
    /**
     * @var array
     */
    private $data;

    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    /**
     * @return mixed|null
     */
    public function execute()
    {
        return $this->data['object'] ?? null;
    }
}
