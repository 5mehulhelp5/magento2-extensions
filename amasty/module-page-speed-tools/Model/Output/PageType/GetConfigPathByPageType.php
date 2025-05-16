<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Model\Output\PageType;

use Magento\Framework\View\Layout;

class GetConfigPathByPageType
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var array
     */
    private $configPathsByPageType;

    /**
     * @var string
     */
    private $pageTypeConfigPath;

    /**
     * @var string
     */
    private $defaultConfigPath;

    /**
     * @var string
     */
    private $pageType;

    public function __construct(
        string $defaultConfigPath,
        array $configPathsByPageType,
        Layout $layout
    ) {
        $this->layout = $layout;
        $this->configPathsByPageType = $configPathsByPageType;
        $this->defaultConfigPath = $defaultConfigPath;
    }

    public function execute(string $pageType = ''): string
    {
        if ($pageType) {
            return $this->configPathsByPageType[$pageType] ?? $this->defaultConfigPath;
        }

        if ($this->pageTypeConfigPath !== null) {
            return $this->pageTypeConfigPath;
        }

        $handles = $this->getPageHandles();
        foreach ($this->configPathsByPageType as $handle => $configPath) {
            if (in_array($handle, $handles)) {
                $this->pageTypeConfigPath = $configPath;
                $this->pageType = $handle;
                break;
            }
        }
        if ($this->pageTypeConfigPath === null) {
            $this->pageTypeConfigPath = $this->defaultConfigPath;
        }

        return $this->pageTypeConfigPath;
    }

    public function getPageType(): ?string
    {
        return $this->pageType;
    }

    private function getPageHandles(): array
    {
        return $this->layout->getUpdate()->getHandles();
    }
}
