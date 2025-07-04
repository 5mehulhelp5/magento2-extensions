<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Setup;

use Amasty\Base\Helper\Deploy;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class RecurringData implements InstallDataInterface
{
    public const DEPLOY_DIR = 'pub';

    /**
     * @var Deploy
     */
    private $deploy;

    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    public function __construct(
        Deploy $deploy,
        ComponentRegistrarInterface $componentRegistrar
    ) {
        $this->deploy = $deploy;
        $this->componentRegistrar = $componentRegistrar;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->deploy->deployFolder(
            $this->componentRegistrar->getPath(
                ComponentRegistrar::MODULE,
                'Amasty_ImageOptimizer'
            ) . DIRECTORY_SEPARATOR . self::DEPLOY_DIR
        );
    }
}
