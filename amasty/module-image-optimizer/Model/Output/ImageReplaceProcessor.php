<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Model\Output;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\OptionSource\ReplaceStrategies;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;
use Amasty\PageSpeedTools\Model\Image\ReplacePatternGroupsPool;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Image\Utils\PathsResolver;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;

class ImageReplaceProcessor implements OutputProcessorInterface
{
    public const REPLACE_PATTERNS_GROUP_KEY = 'image_optimizer';

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    /**
     * @var ReplaceConfig\ReplaceConfigFactory
     */
    private $replaceConfigFactory;

    /**
     * @var ReplaceConfig\ReplaceConfig
     */
    private $replaceConfig;

    /**
     * @var ReplacePatternGroupsPool
     */
    private $replacePatternsResolver;

    /**
     * @var PathsResolver
     */
    private $imagePathsResolver;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    public function __construct(
        LazyConfigProvider $lazyConfigProvider,
        ReplacerCompositeInterface $imageReplacer,
        ReplaceConfig\ReplaceConfigFactory $replaceConfigFactory,
        ReplacePatternGroupsPool $replacePatternsResolver,
        PathsResolver $imagePathsResolver
    ) {
        $this->imageReplacer = $imageReplacer;
        $this->replaceConfigFactory = $replaceConfigFactory;
        $this->replacePatternsResolver = $replacePatternsResolver;
        $this->imagePathsResolver = $imagePathsResolver;
        $this->lazyConfigProvider = $lazyConfigProvider;
    }

    public function process(string &$output): bool
    {
        if ($this->getLazyConfig()->getData('is_lazy')) {
            return true;
        }

        $this->processImages($output);

        return true;
    }

    public function processImages(string &$output)
    {
        $replaceStrategy = $this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::REPLACE_STRATEGY);
        if ($replaceStrategy !== ReplaceStrategies::NONE) {
            $tempOutput = preg_replace('/<script.*?>.*?<\/script.*?>/is', '', $output);
            $replacePatterns = $this->replacePatternsResolver->getByKey(static::REPLACE_PATTERNS_GROUP_KEY);
            foreach ($replacePatterns as $replacePattern) {
                if (preg_match_all('/' . $replacePattern->getPattern() . '/is', $tempOutput, $images)) {
                    $output = $this->modifyOutputByPattern($replacePattern, $output, $images);
                    $tempOutput = preg_replace('/<script.*?>.*?<\/script.*?>/is', '', $output);
                }
            }
        }
    }

    protected function getLazyConfig(): DataObject
    {
        return $this->lazyConfigProvider->get();
    }

    protected function getReplaceConfig(): DataObject
    {
        if ($this->replaceConfig === null) {
            $this->replaceConfig = $this->replaceConfigFactory->create();
        }

        return $this->replaceConfig;
    }

    private function modifyOutputByPattern(
        ReplaceConfigInterface $replacePattern,
        string $output,
        array $images
    ): string {
        foreach ($images[0] as $key => $image) {
            $replaceImagesIgnoreList = $this->getReplaceConfig()
                ->getData(ReplaceConfig\ReplaceConfig::REPLACE_IMAGES_IGNORE_LIST);
            if ($this->skipIfContain($image, $replaceImagesIgnoreList)) {
                continue;
            }

            $imgPaths = $this->imagePathsResolver->resolve($replacePattern, $images, $key);
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

        return $output;
    }

    private function skipIfContain(string $searchString, array $list): bool
    {
        $skip = false;
        foreach ($list as $item) {
            if (strpos($searchString, $item) !== false) {
                $skip = true;
                break;
            }
        }

        return $skip;
    }
}
