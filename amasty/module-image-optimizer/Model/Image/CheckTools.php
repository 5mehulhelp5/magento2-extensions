<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\Data\ImageSettingInterface;
use Amasty\ImageOptimizer\Model\Command\CommandProvider;

class CheckTools
{
    /**
     * @var CommandProvider[] ['provider_code' => CommandProvider, ...]
     */
    private $commandProviders;

    /**
     * @var array[] ['tool_code' => ['name' => 'tool_name', 'command' => 'tool_command'], ...]
     */
    private $tools;

    /**
     * @var string[] ['extension' => 'extension_name', ...]
     */
    private $extensions;

    public function __construct(
        ?CommandProvider $jpegCommandProvider, // @deprecated
        ?CommandProvider $pngCommandProvider, // @deprecated
        ?CommandProvider $gifCommandProvider, // @deprecated
        ?CommandProvider $webpCommandProvider, // @deprecated
        array $commandProviders,
        array $tools,
        array $extensions
    ) {
        $this->commandProviders = $commandProviders;
        $this->tools = $tools;
        $this->extensions = $extensions;
    }

    public function check(ImageSettingInterface $model): array
    {
        $errors = [];
        foreach ($this->commandProviders as $code => $provider) {
            if ($setting = $model->hasData($code) ? (string)$model->getData($code) : null) {
                $tool = $provider->get($setting);
                if ($tool && !$tool->isAvailable()) {
                    $errors[] = $tool->getUnavailableReason();
                }
            }
        }

        return $errors;
    }

    public function getUnavailableTools(): array
    {
        $errors = [];
        foreach ($this->commandProviders as $code => $provider) {
            if ($toolData = $this->tools[$code] ?? null) {
                $tool = $provider->get($toolData['command']);
                if ($tool && !$tool->isAvailable()) {
                    $errors[] = $toolData['name'];
                }
            }
        }
        foreach ($this->extensions as $extension => $name) {
            if (!extension_loaded($extension)) {
                $errors[] = $name;
            }
        }

        return $errors;
    }
}
