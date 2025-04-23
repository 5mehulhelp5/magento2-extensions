<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */

namespace Olegnax\InstagramFeedPro\Model\DynamicStyle;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface as ViewLayout;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Olegnax\InstagramFeedPro\Helper\Helper;

class Generator
{
    const TEMPLATE = 'Olegnax_InstagramFeedPro::styles_global.phtml';
    const XML_PATH_DYNAMIC_FILE_ARG = 'olegnax_instagram_pro_appearance/appearance_custom/dynamic_file_arg';
    const XML_PATH_DYNAMIC_ENABLED = 'olegnax_instagram_pro_appearance/general/enabled';
    const XML_PATH_DYNAMIC_INLINE = 'olegnax_instagram_pro_appearance/general/inline_css';
    const DYNAMIC_DIR = 'ox_instagram/dynamic';
    const FILE_PATH = '%s/%s/style_%s.css';

    /**
     * Store Manager
     *
     * @var StoreManager
     */
    protected $_storeManager;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $_registry;

    /**
     * File
     *
     * @var File
     */
    protected $_file;

    /**
     * View Layout
     *
     * @var ViewLayout
     */
    protected $_viewLayout;

    /**
     * Store Manager
     *
     * @var MessageManager
     */
    protected $_messageManager;
    /**
     * @var Helper
     */
    protected $_helper;
    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * Generator constructor.
     * @param StoreManager $storeManager
     * @param Registry $registry
     * @param File $file
     * @param DirectoryList $filesystem
     * @param ViewLayout $viewLayout
     * @param MessageManager $messageManager
     * @param Helper $helper
     */
    public function __construct(
        StoreManager $storeManager,
        Registry $registry,
        File $file,
        DirectoryList $filesystem,
        ViewLayout $viewLayout,
        MessageManager $messageManager,
        Helper $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->_file = $file;
        $this->_directoryList = $filesystem;
        $this->_viewLayout = $viewLayout;
        $this->_messageManager = $messageManager;
        $this->_helper = $helper;
    }

    /**
     * @param int|null $website
     * @param int|null $store
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws LocalizedException
     */
    public function generate($website, $store)
    {
        if (!empty($store)) {
            $this->generateStore($store);
        } elseif (!empty($website)) {
            $this->generateWebsite($website);
        } else {
            $this->generateAll();
        }
    }

    /**
     * @param int|null $id
     * @throws NoSuchEntityException
     * @throws FileSystemException
     */
    private function generateStore($id)
    {
        $store = $this->_storeManager->getStore($id);
        if (!$store->isActive()
            || !$this->_helper->getConfig(static::XML_PATH_DYNAMIC_ENABLED, $id)
            || $this->_helper->getConfig(static::XML_PATH_DYNAMIC_INLINE, $id)
        ) {
            return;
        }
        $storeCode = $store->getCode();
        $dynamicFile = sprintf(
            static::FILE_PATH,
            $this->_directoryList->getPath(DirectoryList::MEDIA),
            static::DYNAMIC_DIR,
            $storeCode
        );

        try {
            $content = $this->_viewLayout->createBlock(
                Template::class,
                '',
                [
                    'data' => [
                        'area' => 'frontend',
                        'dynamic_store_code' => $storeCode,
                        'template' => static::TEMPLATE,
                    ],
                ]
            )->toHtml();

            if (!empty($content)) {
                $content = preg_replace('/[\r\n\t]/', ' ', $content);
                $content = preg_replace('/[\r\n\t ]{2,}/', ' ', $content);
                $content = preg_replace('/\s+([:;{}])\s+/', '\1', $content);
                $content = preg_replace('/<[^<>]+>(.*?)<\/[^<>]+>/m', '/* Forbidden tags in styles */', $content);
            }
            $this->_file->createDirectory(dirname($dynamicFile), 0775);
            $this->_file->filePutContents($dynamicFile, $content);

            $dynamic_file_arg = abs((int)$this->_helper->getSystemValue(
                static::XML_PATH_DYNAMIC_FILE_ARG,
                null,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            ));
            $dynamic_file_arg++;
            $this->_helper->setSystemValue(
                static::XML_PATH_DYNAMIC_FILE_ARG,
                $dynamic_file_arg,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
        } catch (Exception $e) {
            $this->_messageManager->addErrorMessage(
                __(
                    'Failed generating file: %1<br/>Message: %2',
                    str_replace(BP, '', $dynamicFile),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @param int|null $id
     * @throws LocalizedException
     */
    private function generateWebsite($id)
    {
        $website = $this->_storeManager->getWebsite($id);
        /** @var array|int[] $stores */
        $stores = $website->getStoreIds();
        if (!empty($stores) && is_array($stores)) {
            foreach ($stores as $store) {
                $this->generateStore($store);
            }
        }
    }

    /**
     * @throws LocalizedException
     */
    private function generateAll()
    {
        $websites = $this->_storeManager->getWebsites(false, false);
        if (!empty($websites) && is_array($websites)) {
            foreach ($websites as $id => $website) {
                $this->generateWebsite($id);
            }
        }
    }
}
