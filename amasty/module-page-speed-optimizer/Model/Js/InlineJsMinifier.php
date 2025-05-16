<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model\Js;

use Magento\Framework\Code\Minifier\Adapter\Js\JShrink;

class InlineJsMinifier
{
    /**
     * @var JShrink
     */
    private $JShrink;

    /**
     * @var string[]
     */
    private $replacements;

    /**
     * @param JShrink $JShrink
     * @param string []$replacements [replacement => regex]
     */
    public function __construct(
        JShrink $JShrink,
        array $replacements = []
    ) {
        $this->JShrink = $JShrink;
        $this->replacements = $replacements;
    }

    public function minify(string $inlineJs): string
    {
        //Storing replacements before minification
        $replaceResults = [];
        foreach ($this->replacements as $replacement => $regEx) {
            $inlineJs = preg_replace_callback(
                $regEx,
                function ($match) use (&$replaceResults, $replacement) {
                    $replaceResults[$replacement][] = $match[0];

                    return $replacement . (count($replaceResults[$replacement]) - 1);
                },
                $inlineJs
            );
        }

        try {
            $inlineJs = $this->JShrink->minify($inlineJs);
        } catch (\Exception $e) {
            null; // Do nothing and chill
        }

        //Restoring replacements after minification
        foreach ($replaceResults as $replacement => $originals) {
            $inlineJs = preg_replace_callback(
                "/$replacement(\d+)/ims",
                function ($match) use ($originals) {
                    return $originals[(int)$match[1]];
                },
                $inlineJs
            );
        }

        return $inlineJs;
    }
}
