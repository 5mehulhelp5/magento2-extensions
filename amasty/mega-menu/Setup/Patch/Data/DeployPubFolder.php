<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Setup\Patch\Data;

use Amasty\Base\Helper\Deploy;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validation\ValidationException;

class DeployPubFolder implements DataPatchInterface
{
    public const STATIC_FILES_FOLDER = 'data/pub';

    /**
     * @var Deploy
     */
    private $pubDeploy;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    public function __construct(
        Deploy $pubDeploy,
        ComponentRegistrar $componentRegistrar
    ) {
        $this->pubDeploy = $pubDeploy;
        $this->componentRegistrar = $componentRegistrar;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): self
    {
        $this->deployModuleFiles();

        return $this;
    }

    private function deployModuleFiles(): void
    {
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Amasty_MegaMenu');

        try {
            $this->pubDeploy->deployFolder($moduleDir . DIRECTORY_SEPARATOR . self::STATIC_FILES_FOLDER);
        } catch (ValidationException $e) {
            null; //skip this step
        }
    }
}
