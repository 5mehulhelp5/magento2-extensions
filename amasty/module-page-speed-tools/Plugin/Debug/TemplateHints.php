<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Page Speed Tools for Magento 2 (System)
 */

namespace Amasty\PageSpeedTools\Plugin\Debug;

use Magento\Developer\Model\TemplateEngine\Decorator\DebugHints;
use Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;

class TemplateHints
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DebugHintsFactory
     */
    private $debugHintsFactory;

    public function __construct(
        RequestInterface $request,
        DebugHintsFactory $debugHintsFactory
    ) {
        $this->request = $request;
        $this->debugHintsFactory = $debugHintsFactory;
    }

    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $result
    ): TemplateEngineInterface {
        if (\Amasty\Base\Debug\VarDump::isAllowed()
            && $this->request->getParam('amoptimizer_template_hints')
        ) {
            return $this->debugHintsFactory->create([
                'subject' => $result,
                'showBlockHints' => true,
            ]);
        }

        return $result;
    }
}
