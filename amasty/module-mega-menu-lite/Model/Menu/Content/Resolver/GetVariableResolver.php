<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Menu\Content\Resolver;

class GetVariableResolver
{
    /**
     * @var ResolverInterface[]
     */
    private $resolvers;

    public function __construct(array $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @param string $code
     * @return ResolverInterface|null
     */
    public function get(string $code): ?ResolverInterface
    {
        return $this->resolvers[$code] ?? null;
    }
}
