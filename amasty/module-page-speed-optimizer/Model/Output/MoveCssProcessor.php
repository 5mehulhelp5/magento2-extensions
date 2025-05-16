<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class MoveCssProcessor implements OutputProcessorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DeviceDetect
     */
    private $deviceDetector;

    /**
     * @var SecureHtmlRenderer
     */
    private $secureRenderer;

    public function __construct(
        ConfigProvider $configProvider,
        DeviceDetect $deviceDetector,
        SecureHtmlRenderer $secureRenderer = null // TODO move to not optional
    ) {
        $this->configProvider = $configProvider;
        $this->deviceDetector = $deviceDetector;
        $this->secureRenderer = $secureRenderer ?? ObjectManager::getInstance()->get(SecureHtmlRenderer::class);
    }

    public function process(string &$output): bool
    {
        $moveStyles = '';
        if ($this->configProvider->isMovePrintCss()) {
            $output = preg_replace_callback(
                '/\<link[^>]*media\s*=\s*["\']+print["\']+[^>]*\>/si',
                function ($print) use (&$moveStyles) {
                    $moveStyles .= $print[0];
                    return '';
                },
                $output
            );
        }

        if ($this->configProvider->isMoveFont()
            && preg_match('/<link[^>]*href\s*=\s*["\']+([^"\']*merged[^"\']*)["\']+[^>]*\>/is', $output, $m)
        ) {
            if ($this->isMoveForCurrentDevice()) {
                $fontLink = str_replace(
                    $this->basename($m[1]),
                    'fonts_' . $this->basename($m[1]),
                    $m[1]
                );
                $moveStyles .= '<link rel="stylesheet"  type="text/css"  media="all" href="' . $fontLink . '" />';
            } else {
                $output = str_ireplace($this->basename($m[1]), 'orig_' . $this->basename($m[1]), $output);
            }
        }

        if (!empty($moveStyles)) {
            $moveStyles = '<noscript id="deferred-css">' . $moveStyles . '</noscript>';
            $moveStyles .= $this->getLoadDeferredStylesScript();

            $output = str_ireplace('</body', $moveStyles . '</body', $output);
        }

        return true;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function basename($file)
    {
        //phpcs:ignore
        return basename($file);
    }

    /**
     * @return bool
     */
    public function isMoveForCurrentDevice()
    {
        return in_array($this->deviceDetector->getDeviceType(), $this->configProvider->getMoveFontForDevice());
    }

    private function getLoadDeferredStylesScript(): string
    {
        $script = <<<SCRIPT
                window.addEventListener('load', () => {
                    const addStylesNode = document.getElementById("deferred-css");
                    const replacement = document.createElement("div");
                    replacement.innerHTML = addStylesNode.textContent;
                    document.body.appendChild(replacement);
                    addStylesNode.parentElement.removeChild(addStylesNode);
                });
        SCRIPT;

        return $this->secureRenderer->renderTag(
            'script',
            [],
            $script,
            false
        );
    }
}
