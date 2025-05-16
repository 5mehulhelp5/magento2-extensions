<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\LazyScript;

class Lozad implements LazyScriptInterface
{
    public function getName(): string
    {
        return (string)__('Lozad Lazy Script');
    }

    public function getType(): string
    {
        return 'lozad';
    }

    public function getCode(): string
    {
        return '<script>
            window.amlazycallback = function () {
                if (typeof window.amLozadInstance !== "undefined") {
                    window.amLozadInstance.observe();
                }
            };
            require(["Amasty_LazyLoad/js/lozad"], function(lozad) {
                window.amLozadInstance = lozad("img[data-amsrc]", {});
                window.amLozadInstance.observe();
            });

            </script>';
    }
}
