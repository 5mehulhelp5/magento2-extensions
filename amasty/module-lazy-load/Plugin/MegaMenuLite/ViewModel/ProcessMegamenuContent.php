<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Plugin\MegaMenuLite\ViewModel;

use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyLoadProcessor;
use Amasty\LazyLoad\Plugin\Catalog\Block\Product\View\Gallery\ProductGalleryReplace;
use Amasty\MegaMenuLite\ViewModel\Tree;
use Amasty\PageSpeedTools\Model\Image\ReplaceByPatternApplier;
use Amasty\PageSpeedTools\Model\Image\ReplacePatternGroupsPool;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Image\Utils\PathsResolver;
use Magento\Framework\App\ObjectManager;

class ProcessMegamenuContent
{
    /**
     * @var array|null
     */
    private $cachedResult = null;

    /**
     * @var LazyConfig
     */
    private $lazyConfig;

    /**
     * @var PathsResolver
     */
    private $pathsResolver;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    /**
     * @var ReplacePatternGroupsPool
     */
    private $replacePatternsResolver;

    /**
     * @var ReplaceByPatternApplier
     */
    private $replaceByPatternApplier;

    public function __construct(
        LazyLoadProcessor $imageReplaceProcessor,
        PathsResolver $pathsResolver,
        ReplacerCompositeInterface $imageReplacer,
        ReplacePatternGroupsPool $replacePatternsResolver,
        ReplaceByPatternApplier $replaceByPatternApplier = null
    ) {
        $this->lazyConfig = $imageReplaceProcessor->getLazyConfig();
        $this->pathsResolver = $pathsResolver;
        $this->imageReplacer = $imageReplacer;
        $this->replacePatternsResolver = $replacePatternsResolver;
        // OM for backward compatibility
        $this->replaceByPatternApplier = $replaceByPatternApplier ?? ObjectManager::getInstance()
            ->get(ReplaceByPatternApplier::class);
    }

    public function afterGetNodesData(Tree $subject, array $result): array
    {
        if (!$this->lazyConfig->getData(LazyConfig::IS_LAZY)) {
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
            $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $node['content']);
            $replacePatterns = $this->replacePatternsResolver->getByKey(LazyLoadProcessor::REPLACE_PATTERNS_GROUP_KEY);
            foreach ($replacePatterns as $replacePattern) {
                if (preg_match_all('/' . $replacePattern->getPattern() . '/is', $tempOutput, $images)) {
                    $node['content'] = $this->replaceImages($replacePattern, $node['content'], $images);
                }
            }
        }
    }

    /**
     * Can't use LazyLoadProcessor directly because the MegaMenu data format is not compatible with lazyload.
     * Copying replace images logic.
     *
     * @param ReplaceConfigInterface $replacePattern
     * @param string $output
     * @param array $images
     *
     * @return string
     */
    private function replaceImages(
        ReplaceConfigInterface $replacePattern,
        string $output,
        array $images
    ): string {
        $userAgentIgnoreList = $this->lazyConfig->getData(LazyConfig::USER_AGENT_IGNORE_LIST);
        foreach ($images[0] as $key => $image) {
            if ($this->lazyConfig->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)
                && !$this->checkIsContains($image, $userAgentIgnoreList)
            ) {
                $imgPaths = $this->pathsResolver->resolve($replacePattern, $images, $key);
                foreach ($imgPaths as $imgPath) {
                    $newImg = $this->imageReplacer->replace(
                        $replacePattern,
                        $image,
                        $imgPath
                    );
                    $output = str_replace($image, $newImg, $output);
                    $image = $newImg;
                }
            }
        }

        return $output;
    }

    private function checkIsContains(string $searchString, array $list): bool
    {
        $contains = false;
        foreach ($list as $item) {
            if (strpos($searchString, $item) !== false) {
                $contains = true;
                break;
            }
        }

        return $contains;
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
