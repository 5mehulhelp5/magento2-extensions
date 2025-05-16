<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Lazy Load for Magento 2 (System)
 */

namespace Amasty\LazyLoad\Model\Output;

use Amasty\LazyLoad\Model\Asset\Collector\PreloadImageCollector;
use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\LazyScript\LazyScriptProvider;
use Amasty\LazyLoad\Model\OptionSource\PreloadStrategy;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplacePatternGroupsPool;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\Img;
use Amasty\PageSpeedTools\Model\Image\ReplacePatterns\ReplaceConfigInterface;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Image\Utils\PathsResolver;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

class LazyLoadProcessor implements OutputProcessorInterface
{
    public const REPLACE_PATTERNS_GROUP_KEY = 'lazy_load';
    public const LAZY_LOAD_PLACEHOLDER = 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABC'
        . 'AQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII="';
    private const REPLACE_SRC_ATTR_REGEX_PATTERN = '\bsrc\s*\=\s*[\'\"]%s[\"\']';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LazyConfigFactory
     */
    private $lazyConfigFactory;

    /**
     * @var LazyConfig|DataObject
     */
    private $lazyConfig;

    /**
     * @var LazyScriptProvider
     */
    private $lazyScriptProvider;

    /**
     * @var PreloadImageCollector
     */
    private $preloadImageCollector;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    /**
     * @var AspectRatioApplier
     */
    private $aspectRatioApplier;

    /**
     * @var ReplacePatternGroupsPool
     */
    private $replacePatternsResolver;

    /**
     * @var PathsResolver
     */
    private $imagePathsResolver;

    public function __construct(
        ConfigProvider $configProvider,
        LazyScriptProvider $lazyScriptProvider,
        LazyConfigFactory $lazyConfigFactory,
        PreloadImageCollector $preloadImageCollector,
        ReplacerCompositeInterface $imageReplacer,
        AspectRatioApplier $aspectRatioApplier,
        ReplacePatternGroupsPool $replacePatternsResolver,
        PathsResolver $imagePathsResolver
    ) {
        $this->configProvider = $configProvider;
        $this->lazyScriptProvider = $lazyScriptProvider;
        $this->lazyConfigFactory = $lazyConfigFactory;
        $this->preloadImageCollector = $preloadImageCollector;
        $this->imageReplacer = $imageReplacer;
        $this->aspectRatioApplier = $aspectRatioApplier;
        $this->replacePatternsResolver = $replacePatternsResolver;
        $this->imagePathsResolver = $imagePathsResolver;
    }

    public function getLazyConfig(): DataObject
    {
        if ($this->lazyConfig === null) {
            $this->lazyConfig = $this->lazyConfigFactory->create();
        }

        return $this->lazyConfig;
    }

    public function setLazyConfig(DataObject $lazyConfig): void
    {
        $this->lazyConfig = $lazyConfig;
    }

    public function process(string &$output): bool
    {
        if ($this->getLazyConfig()->getData(LazyConfig::IS_LAZY)) {
            $this->processLazyImages($output);
            if ($this->getLazyConfig()->hasData(LazyConfig::LAZY_SCRIPT)) {
                $this->addLazyScript($output, $this->getLazyConfig()->getData(LazyConfig::LAZY_SCRIPT));
            }
        }

        return true;
    }

    public function processLazyImages(string &$output): void
    {
        $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $output);
        $replacePatterns = $this->replacePatternsResolver->getByKey(static::REPLACE_PATTERNS_GROUP_KEY);
        foreach ($replacePatterns as $replacePattern) {
            if (preg_match_all('/' . $replacePattern->getPattern() . '/is', $tempOutput, $images)) {
                $output = $this->modifyOutputByPattern($replacePattern, $output, $images);
            }
        }
    }

    private function modifyOutputByPattern(
        ReplaceConfigInterface $replacePattern,
        string $output,
        array $images
    ): string {
        $index = 0;
        $userAgentIgnoreList = $this->getLazyConfig()->getData(LazyConfig::USER_AGENT_IGNORE_LIST);
        $lazyIgnoreList = $this->getLazyConfig()->getData(LazyConfig::LAZY_IGNORE_LIST);

        if ($replacePattern->getPatternName() === Img::NAME) {
            $index = $this->processPreload($replacePattern, $output, $images);
        }

        $lastIndex = array_key_last($images[0]);
        while ($index < $lastIndex) {
            $newImg = $origImg = $images[0][$index];
            $imgPaths = $this->imagePathsResolver->resolve($replacePattern, $images, $index);
            if (!$this->checkIsContains($origImg, $lazyIgnoreList)) {
                if ($replacePattern->getPatternName() === Img::NAME
                    && !$this->isThirdPartyAttribute($replacePattern, $images, $index)
                ) {
                    $newImg = $this->processLazyLoadReplace($replacePattern, $images, $index, $origImg);
                }
                $newImg = preg_replace('/srcset=[\"\'\s]+(.*?)[\"\']+/is', '', $newImg);
            }
            if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)
                && !$this->checkIsContains($origImg, $userAgentIgnoreList)
            ) {
                foreach ($imgPaths as $imgPath) {
                    $newImg = $this->imageReplacer->replace(
                        $replacePattern,
                        $newImg,
                        $imgPath
                    );
                }
            }
            $output = str_replace($origImg, $newImg, $output);
            $index++;
        }

        return $output;
    }

    private function processPreload(
        ReplaceConfigInterface $replacePattern,
        string &$output,
        array $images
    ): int {
        $userAgentIgnoreList = $this->getLazyConfig()->getData(LazyConfig::USER_AGENT_IGNORE_LIST);
        $preloadStrategy = $this->getLazyConfig()->getData(LazyConfig::LAZY_PRELOAD_STRATEGY);
        $skipImagesCount = $this->getLazyConfig()->getData(LazyConfig::LAZY_SKIP_IMAGES);
        while ($skipImagesCount !== 0) {
            $index = key($images[0]);
            if ($index === null) {
                break;
            }
            $image = current($images[0]);
            $imgPaths = $this->imagePathsResolver->resolve($replacePattern, $images, $index);
            foreach ($imgPaths as $imgPath) {
                //skip images strategy can be used only if user agent is disabled
                if ($preloadStrategy === PreloadStrategy::SKIP_IMAGES) {
                    $this->preloadImageCollector->addImageAsset($imgPath);
                } elseif (!$this->checkIsContains($image, $userAgentIgnoreList)) {
                    $newImg = $this->imageReplacer->replace(
                        $replacePattern,
                        $image,
                        $imgPath
                    );
                    $output = str_replace($image, $newImg, $output);
                    if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)) {
                        $newImgPath = $this->imageReplacer->replaceImagePath(
                            $replacePattern,
                            $imgPath
                        );
                        $this->preloadImageCollector->addImageAsset($newImgPath);
                    }
                }
            }
            $skipImagesCount--;
            next($images[0]);
        }

        return isset($index) ? ++$index : 0;
    }

    private function processLazyLoadReplace(
        ReplaceConfigInterface $replacePattern,
        array $images,
        int $index,
        string $origImg
    ): string {
        $regexpGroupByName = $replacePattern->getGroupByName();
        $imgSrc = $images[$regexpGroupByName['src']][$index];
        $regex = '/'
            . sprintf(self::REPLACE_SRC_ATTR_REGEX_PATTERN, preg_quote($imgSrc, '/'))
            . '/is';

        $newImg = preg_replace(
            $regex,
            self::LAZY_LOAD_PLACEHOLDER . ' data-amsrc="' . $imgSrc . '"',
            $origImg
        );

        $aspectRatioIncludeList = $this->configProvider->getAspectRatioList();
        if ($this->configProvider->isAspectRatioEnabled()
            && $this->checkIsContains($origImg, $aspectRatioIncludeList)
        ) {
            $this->aspectRatioApplier->apply($newImg, $imgSrc);
        }

        return $newImg;
    }

    public function addLazyScript(&$output, $lazyScriptType)
    {
        $lazy = '<script>window.amlazy = function() {'
            . 'if (typeof window.amlazycallback !== "undefined") {'
            . 'setTimeout(window.amlazycallback, 500);setTimeout(window.amlazycallback, 1500);}'
            . '}</script>';
        if ($lazyScript = $this->lazyScriptProvider->get($lazyScriptType)) {
            $lazy .= $lazyScript->getCode();
        }

        $output = str_ireplace('</body', $lazy . '</body', $output);
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

    /**
     * Only for Img regex
     *
     * @param ReplaceConfigInterface $replacePattern
     * @param array $images
     * @param int $index
     *
     * @return bool
     */
    private function isThirdPartyAttribute(
        ReplaceConfigInterface $replacePattern,
        array $images,
        int $index
    ): bool {
        $result = false;
        $imgAttributes = $this->getLazyConfig()
            ->getData(LazyConfig::IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
        if ($imgAttributes) {
            $key = null;
            $regexpGroupByName = $replacePattern->getGroupByName();
            foreach ($regexpGroupByName as $groupName => $groupNumber) {
                if ($groupName != 'src' && !empty($images[$groupName][$index])) {
                    $key = $groupNumber;
                    break;
                }
            }
            $result = $key && !empty($images[$key][$index]);
        }

        return $result;
    }
}
