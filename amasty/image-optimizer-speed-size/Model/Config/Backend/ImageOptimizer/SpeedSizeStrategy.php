<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer Speed Size for Magento 2
 */

namespace Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\ImageOptimizer;

use Amasty\ImageOptimizerSpeedSize\Model\Config\Backend\Validation\ValidatorComposite;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class SpeedSizeStrategy extends Value
{
    public const SPEED_SIZE_STRATEGY_KEY = '2';

    /**
     * @var ValidatorComposite
     */
    private $validator;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ValidatorComposite $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->validator = $validator;
    }

    protected function _getValidationRulesBeforeSave()
    {
        if ($this->getValue() !== self::SPEED_SIZE_STRATEGY_KEY) {
            return null;
        }

        return $this->validator;
    }
}
