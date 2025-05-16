<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Plugin\Config\Model\Config\Structure;

use Magento\Config\Model\Config\ScopeDefiner;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\ElementInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\ArrayManager;

class AddSpeedSizeDependency
{
    private const LAZY_LOAD_SECTION_ID = 'amlazyload';
    private const PRELOAD_STRATEGY_PATH = 'preload_images_strategy/depends/fields/speed_size_enabled';
    private const USER_AGENT_COMMENT_PATH
        = 'children/images_user_agent/children/replace_images_using_user_agent/comment';
    private const SPEED_SIZE_DEPENDENCY = [
        'id' => 'amlazyload/lazy_advanced/speed_size_enabled',
        'value' => '0',
        '_elementType' => 'field',
        'dependPath' => [
            'amlazyload',
            'lazy_advanced',
            'speed_size_enabled'
        ]
    ];

    /**
     * @var ScopeDefiner
     */
    private $scopeDefiner;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        ScopeDefiner $scopeDefiner,
        ArrayManager $arrayManager,
        Manager $moduleManager
    ) {
        $this->scopeDefiner = $scopeDefiner;
        $this->arrayManager = $arrayManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetElementByPathParts(
        Structure $subject,
        ElementInterface $result,
        array $pathParts
    ): ElementInterface {
        $moduleSection = $result->getData();
        if (!empty($moduleSection['id'])
            && $moduleSection['id'] === self::LAZY_LOAD_SECTION_ID
            && $this->moduleManager->isEnabled('Amasty_ImageOptimizer')
        ) {
            $this->addUserAgentComment($moduleSection);
            $this->addPreloadStrategyDependencies($moduleSection);
            $result->setData($moduleSection, $this->scopeDefiner->getScope());
        }

        return $result;
    }

    private function addUserAgentComment(array &$moduleSection): void
    {
        $moduleSection = $this->arrayManager->set(
            self::USER_AGENT_COMMENT_PATH,
            $moduleSection,
            __('Please note that the User Agent functionality does not work when SpeedSize is enabled.')
        );
    }

    private function addPreloadStrategyDependencies(array &$moduleSection): void
    {
        $layLoadSection = &$moduleSection['children']['lazy_advanced']['children'];
        $childGroups = ['lazy_home', 'lazy_categories', 'lazy_products', 'lazy_cms'];

        $layLoadSection = $this->arrayManager->set(
            self::PRELOAD_STRATEGY_PATH,
            $layLoadSection,
            self::SPEED_SIZE_DEPENDENCY
        );
        foreach ($childGroups as $group) {
            $layLoadSection = $this->arrayManager->set(
                $group . '/children/' . self::PRELOAD_STRATEGY_PATH,
                $layLoadSection,
                self::SPEED_SIZE_DEPENDENCY
            );
        }
    }
}
