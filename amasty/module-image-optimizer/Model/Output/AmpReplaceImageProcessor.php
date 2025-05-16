<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Output;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\PageSpeedTools\Model\Image\ReplacePatternGroupsPool;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Image\Utils\PathsResolver;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DataObject;

class AmpReplaceImageProcessor extends ImageReplaceProcessor
{
    public const REPLACE_PATTERNS_GROUP_KEY = 'amp_image_optimizer';

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        LazyConfigProvider $lazyConfigProvider,
        ReplacerCompositeInterface $imageReplacer,
        ReplaceConfig\ReplaceConfigFactory $replaceConfigFactory,
        ReplacePatternGroupsPool $replacePatternsResolver,
        PathsResolver $imagePathsResolver,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct(
            $lazyConfigProvider,
            $imageReplacer,
            $replaceConfigFactory,
            $replacePatternsResolver,
            $imagePathsResolver
        );
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Prevent is_lazy checking on AMP pages
     * @return DataObject
     */
    public function getLazyConfig(): DataObject
    {
        return $this->dataObjectFactory->create();
    }
}
