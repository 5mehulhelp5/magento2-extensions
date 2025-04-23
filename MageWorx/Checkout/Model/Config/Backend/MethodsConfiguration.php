<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Model\Config\Backend;

use Exception;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Config\Model\ResourceModel\Config\Data as ConfigDataResource;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\Checkout\Helper\PaymentMethods as Helper;
use Magento\Config\Model\ResourceModel\Config\Data\Collection as ConfigDataCollection;

/**
 * Class MethodsConfiguration
 *
 * Backend model for payment methods
 *
 * @method getFieldConfig()
 */
class MethodsConfiguration extends \Magento\Framework\App\Config\Value
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var RequestDataInterface
     */
    protected $requestData;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Store
     */
    protected $store;

    /**
     * MethodsConfiguration constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param Helper $helper
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param ConfigDataResource $resource
     * @param ConfigDataCollection $resourceCollection
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Helper $helper,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        ConfigDataResource $resource,
        ConfigDataCollection $resourceCollection,
        array $data = []
    ) {
        $this->helper          = $helper;
        $this->uploaderFactory = $uploaderFactory;
        $this->requestData     = $requestData;
        $this->filesystem      = $filesystem;
        $this->mediaDirectory  = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager    = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Prepare data before save
     *
     * @return void
     * @throws Exception
     */
    public function beforeSave(): void
    {
        /** @var array $value */
        $value = $this->getValue();
        $this->uploadImages($value);
        $value = $this->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Upload images
     *
     * @param array $data
     * @throws LocalizedException
     */
    private function uploadImages(array &$data = []): void
    {
        foreach ($data as $key => $datum) {
            if (empty($datum['image']) || is_string($datum['image'])) {
                continue;
            }

            if (!empty($datum['image']['delete'])) {
                $data[$key]['image'] = '';
                continue;
            }

            if (!empty($datum['image']['old']) && empty($datum['image']['tmp_name'])) {
                $data[$key]['image'] = $datum['image']['old'];
                continue;
            }

            if (empty($datum['image']['tmp_name']) || !empty($datum['image']['error'])) {
                $data[$key]['image'] = '';
                continue;
            }

            $file = $this->getFileData($datum['image']);
            if (!empty($file)) {
                $uploadDir = $this->getUploadDir();
                try {
                    /** @var Uploader $uploader */
                    $uploader = $this->uploaderFactory->create(['fileId' => $file]);
                    $uploader->setAllowedExtensions($this->getAllowedExtensions());
                    $uploader->setAllowRenameFiles(true);
                    $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                    $result = $uploader->save($uploadDir);
                } catch (\Exception $e) {
                    throw new LocalizedException(__('Unable to upload a file: %1', $e->getMessage()));
                }

                $filename = $result['file'];
                if ($filename) {
                    if ($this->addWhetherScopeInfo()) {
                        $filename = $this->prependScopeInfo($filename);
                    }
                    $data[$key]['image'] = $filename;
                }
            }
        }
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throws LocalizedException
     */
    protected function getUploadDir(): string
    {
        $fieldConfig = $this->getFieldConfig();

        if (!array_key_exists('upload_dir', $fieldConfig)) {
            throw new LocalizedException(
                __('The base directory to upload file is not specified.')
            );
        }

        if (is_array($fieldConfig['upload_dir'])) {
            $uploadDir = $fieldConfig['upload_dir']['value'];
            if (array_key_exists('scope_info', $fieldConfig['upload_dir'])
                && $fieldConfig['upload_dir']['scope_info']
            ) {
                $uploadDir = $this->appendScopeInfo($uploadDir);
            }

            if (array_key_exists('config', $fieldConfig['upload_dir'])) {
                $uploadDir = $this->getUploadDirPath($uploadDir);
            }
        } else {
            $uploadDir = (string)$fieldConfig['upload_dir'];
        }

        return $uploadDir;
    }

    /**
     * Retrieve upload directory path
     *
     * @param string $uploadDir
     * @return string
     */
    protected function getUploadDirPath(string $uploadDir): string
    {
        return $this->mediaDirectory->getAbsolutePath($uploadDir);
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function addWhetherScopeInfo(): bool
    {
        $fieldConfig = $this->getFieldConfig();
        $dirParams   = array_key_exists('upload_dir', $fieldConfig) ? $fieldConfig['upload_dir'] : [];

        $result = is_array($dirParams) && array_key_exists('scope_info', $dirParams) && $dirParams['scope_info'];

        return $result;
    }

    /**
     * Prepend path with scope info
     *
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'
     *
     * @param string $path
     * @return string
     */
    protected function prependScopeInfo(string $path): string
    {
        $scopeInfo = $this->getScope();
        if (ScopeConfigInterface::SCOPE_TYPE_DEFAULT != $this->getScope()) {
            $scopeInfo .= '/' . $this->getScopeId();
        }

        return $scopeInfo . '/' . $path;
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    protected function appendScopeInfo(string $path): string
    {
        $path .= '/' . $this->getScope();
        if (ScopeConfigInterface::SCOPE_TYPE_DEFAULT != $this->getScope()) {
            $path .= '/' . $this->getScopeId();
        }

        return $path;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }

    /**
     * Parse file's data
     *
     * @param array $data
     * @return array
     */
    private function getFileData(array $data): array
    {
        $file = [];
        if (!empty($data['tmp_name'])) {
            $file['tmp_name'] = $data['tmp_name'];
            $file['name']     = isset($data['value']) ? $data['value'] : $data['name'];
        }

        return $file;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     * @throws Exception
     */
    public function makeStorableArrayFieldValue($value): string
    {
        if ($this->helper->isEncodedArrayFieldValue($value)) {
            $value = $this->helper->decodeArrayFieldValue($value);
        }
        $value = $this->helper->serializeValue($value);

        return $value;
    }

    /**
     * Process data after load
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _afterLoad(): void
    {
        $value = $this->getValue();
        $value = $this->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     * @throws LocalizedException
     */
    public function makeArrayFieldValue($value): array
    {
        $value = $this->helper->unserializeValue($value);
        if (!$this->helper->isEncodedArrayFieldValue($value)) {
            $value = $this->helper->encodeArrayFieldValue($value);
        }

        foreach ($value as &$item) {
            $item['image'] = isset($item['image']) && is_string($item['image']) ? $item['image'] : '';
        }

        return $value;
    }
}
