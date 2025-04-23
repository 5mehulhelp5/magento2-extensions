<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\ComponentDeclaration;

use Amasty\MegaMenuLite\Api\Component\ComponentDeclarationInterface;

class DeclarationPool
{
    /**
     * @var ComponentDeclarationInterface[]
     */
    private $componentDeclarations;

    /**
     * @param ComponentDeclarationInterface[] $componentDeclarations
     */
    public function __construct(
        array $componentDeclarations = []
    ) {
        $this->componentDeclarations = $componentDeclarations;
    }

    public function getComponentDeclarations(): array
    {
        $declarations = [];

        foreach ($this->componentDeclarations as $componentName => $componentDeclaration) {
            if (!$componentDeclaration instanceof ComponentDeclarationInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The ComponentDeclaration instance %s must implement %s',
                        get_class($componentDeclaration),
                        ComponentDeclarationInterface::class
                    )
                );
            }

            if ($declaration = $componentDeclaration->getDeclaration()) {
                $declarations[$componentName] = $declaration;
            }
        }

        return $declarations;
    }
}
