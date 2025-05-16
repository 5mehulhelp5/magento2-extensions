<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\MegaMenuLite\ViewModel;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ImageReplaceProcessor;
use Amasty\ImageOptimizer\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\MegaMenuLite\ViewModel\Tree;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Magento\Framework\App\ObjectManager;

class ProcessMegamenuContent
{
    /**
     * @var ImageReplaceProcessor
     */
    private $imageReplaceProcessor;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var array|null
     */
    private $cachedResult = null;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        ImageReplaceProcessor $imageReplaceProcessor,
        LazyConfigProvider $lazyConfigProvider,
        ReplaceByPatternApplier $replaceByPatternApplier = null
    ) {
        $this->imageReplaceProcessor = $imageReplaceProcessor;
        $this->lazyConfigProvider = $lazyConfigProvider;
        // OM for backward compatibility
        $this->replaceByPatternApplier = $replaceByPatternApplier ?? ObjectManager::getInstance()
            ->get(ReplaceByPatternApplier::class);
    }

    public function afterGetNodesData(Tree $subject, array $result): array
    {
        if ($this->lazyConfigProvider->get()->getData('is_lazy')) {
            return $result;
        }

        if ($this->cachedResult === null) {
            if (!empty($result['elems'])) {
                foreach ($result['elems'] as &$node) {
                    $this->processNode($node);
                }
            }
            $this->cachedResult = $result;
        }

        return $this->cachedResult;
    }

    private function processNode(array &$node): void
    {
        if (!empty($node['icon'])) {
            $node['icon'] = $this->getCategoryImage($node['icon']);
        }
        if (!empty($node['elems'])) {
            foreach ($node['elems'] as &$child) {
                $this->processNode($child);
            }
        }

        if (!empty($node['content'])) {
            $this->imageReplaceProcessor->process($node['content']);
        }
    }

    private function getCategoryImage(string $image): string
    {
        $images = [$image];
        $this->replaceByPatternApplier->execute(
            ProductGalleryReplace::REPLACE_PATTERN_GROUP,
            $images
        );

        return $images[0];
    }
}
