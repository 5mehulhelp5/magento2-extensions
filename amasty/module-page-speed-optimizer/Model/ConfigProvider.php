<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Google Page Speed Optimizer Base for Magento 2
 */

namespace Amasty\PageSpeedOptimizer\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amoptimizer/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const XPATH_ENABLED = 'general/enabled';
    public const IS_CLOUD = 'javascript/is_cloud';
    public const BUNDLING_FILES = 'javascript/bundling_files';
    public const MOVE_JS = 'javascript/movejs';
    public const MOVE_JS_EXCLUDE_URL = 'javascript/movejs_exclude_url';
    public const MOVE_JS_EXCLUDE_PART = 'javascript/movejs_exclude_part';
    public const ADMINHTML_JS_MERGE_BUNDLE = 'javascript/merge_and_bundle_adminhtml';
    public const MINIFY_JS_IN_PHTML = 'javascript/minify_js_phtml';
    public const MINIFY_JS_IN_PHTML_BLACKLIST = 'javascript/minify_js_phtml_blacklist';
    public const EXCLUDE_URLS_FROM_MERGE_BUNDLE = 'javascript/not_merge_and_bundle_urls';
    public const MERGE_CSS_ADMINHTML = 'css/merge_css_adminhtml';
    public const EXCLUDE_URLS_FROM_MERGE_CSS = 'css/not_merge_css_urls';
    public const MOVE_PRINT_CSS = 'css/move_print';
    public const SERVER_PUSH_ENABLED = 'server_push/enabled';
    public const SERVER_PUSH_TYPES = 'server_push/server_push_types';
    public const SERVER_PUSH_EXCLUDE = 'server_push/server_push_exclude';
    public const MOVE_FONT = 'css/move_font';
    public const FONT_IGNORE_LIST = 'css/font_ignore_list';
    public const MOVE_FONT_FOR_DEVICE = 'css/move_font_for_device';
    /**#@-*/

    public const BUNDLING_TYPE = 'javascript/bundling_type';
    public const BUNDLE_STEP = 'javascript/bundle_step';
    public const BUNDLE_HASH = 'javascript/bundle_hash';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::XPATH_ENABLED);
    }

    /**
     * @return bool
     */
    public function isCloud()
    {
        return $this->isSetFlag(self::IS_CLOUD);
    }

    public function getBundlingFiles()
    {
        $bundlingFiles = $this->getValue(self::BUNDLING_FILES);
        if (!empty($bundlingFiles)) {
            $data = json_decode($bundlingFiles, true);
            if (json_last_error()) {
                return [];
            }

            return $data;
        }
        return [];
    }

    /**
     * @return bool
     */
    public function isMoveJS()
    {
        return $this->isSetFlag(self::MOVE_JS);
    }

    /**
     * @return bool
     */
    public function adminhtmlJsMergeBundle()
    {
        return $this->isSetFlag(self::ADMINHTML_JS_MERGE_BUNDLE);
    }

    /**
     * @return array
     */
    public function getExcludeUrlsFromMergeBundleJs()
    {
        return $this->convertStringToArray($this->getValue(self::EXCLUDE_URLS_FROM_MERGE_BUNDLE));
    }

    /**
     * @return bool
     */
    public function adminhtmlMergeCss()
    {
        return $this->isSetFlag(self::MERGE_CSS_ADMINHTML);
    }

    /**
     * @return array
     */
    public function getExcludeUrlsFromMergeCss()
    {
        return $this->convertStringToArray($this->getValue(self::EXCLUDE_URLS_FROM_MERGE_CSS));
    }

    /**
     * @return array
     */
    public function getMoveJsExcludeUrl()
    {
        return $this->convertStringToArray($this->getValue(self::MOVE_JS_EXCLUDE_URL));
    }

    /**
     * @return array
     */
    public function getMoveJsExcludePart()
    {
        return $this->convertStringToArray($this->getValue(self::MOVE_JS_EXCLUDE_PART));
    }

    /**
     * @return bool
     */
    public function isMovePrintCss()
    {
        return $this->isSetFlag(self::MOVE_PRINT_CSS);
    }

    /**
     * @deprecated
     * @see self::getServerPushType
     */
    public function isServerPushEnabled(): bool
    {
        return (bool)$this->getServerPushType();
    }

    public function getServerPushType(): int
    {
        return (int)$this->getValue(self::SERVER_PUSH_ENABLED);
    }

    public function getServerPushAssetTypes(): array
    {
        $assetTypes = (string)$this->getValue(self::SERVER_PUSH_TYPES);

        return array_filter(explode(',', $assetTypes));
    }

    public function getServerPushIgnoreList()
    {
        return $this->convertStringToArray($this->getValue(self::SERVER_PUSH_EXCLUDE));
    }

    /**
     * @return bool
     */
    public function isMoveFont()
    {
        return $this->isSetFlag(self::MOVE_FONT);
    }

    /**
     * @return array
     */
    public function getFontIgnoreList()
    {
        return $this->convertStringToArray($this->getValue(self::FONT_IGNORE_LIST));
    }

    public function getMoveFontForDevice(): array
    {
        $deviceTypes = (string)$this->getValue(self::MOVE_FONT_FOR_DEVICE);

        return array_filter(explode(',', $deviceTypes));
    }

    public function getConfig($path)
    {
        return $this->getValue($path);
    }

    public function getCustomValue($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function getBundlingType()
    {
        return (int)$this->getValue(self::BUNDLING_TYPE);
    }

    public function getBundleStep()
    {
        return (int)$this->getValue(self::BUNDLE_STEP);
    }

    public function getBundleHash()
    {
        return $this->getValue(self::BUNDLE_HASH);
    }

    /**
     * @return bool
     */
    public function isMifiniedJs()
    {
        return (bool)$this->scopeConfig->getValue('dev/js/minify_files');
    }

    /**
     * @return bool
     */
    public function isMinifiedJsInPhtml()
    {
        return (bool)$this->getValue(self::MINIFY_JS_IN_PHTML);
    }

    /**
     * @return array
     */
    public function getMinifyJsPhtmlBlacklist()
    {
        return $this->convertStringToArray($this->getValue(self::MINIFY_JS_IN_PHTML_BLACKLIST));
    }

    /**
     * @param string $data
     * @param string $separator
     *
     * @return array
     */
    public function convertStringToArray($data, $separator = PHP_EOL)
    {
        if (empty($data)) {
            return [];
        }

        return array_filter(array_map('trim', explode($separator, $data)));
    }
}
