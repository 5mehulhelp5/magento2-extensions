<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Shop by Base for Magento 2 (System)
 */

namespace Amasty\ShopbyBase\Model;

use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Helper\FilterSetting;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting as OptionSettingResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option;
use Magento\Framework\Model\AbstractModel;

class OptionSettingRepository implements OptionSettingRepositoryInterface
{
    /**
     * @var OptionSettingResource
     */
    private $resource;

    /**
     * @var OptionSettingFactory
     */
    private $factory;

    /**
     * @var OptionSettingResource\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Option\CollectionFactory
     */
    private $optionCollectionFactory;

    public function __construct(
        OptionSettingResource $resource,
        OptionSettingFactory $factory,
        ResourceModel\OptionSetting\CollectionFactory $collectionFactory,
        Option\CollectionFactory $optionCollectionFactory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->optionCollectionFactory = $optionCollectionFactory;
    }

    /**
     * @return OptionSettingInterface
     * @throws NoSuchEntityException
     */
    public function get($value, $field = null)
    {
        $entity = $this->factory->create();
        $this->resource->load($entity, $value, $field);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(__('Requested option setting doesn\'t exist'));
        }

        return $entity;
    }

    /**
     * @param string $filterCode
     * @param int $optionId
     * @param int $storeId
     * @return OptionSettingInterface
     * @deprecared use getByCode instead
     */
    public function getByParams($filterCode, $optionId, $storeId)
    {
        return $this->getByCode(FilterSetting::convertToAttributeCode($filterCode), (int) $optionId, (int) $storeId);
    }

    public function getByCode(string $attributeCode, int $optionId, int $storeId): OptionSettingInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addLoadFilters($attributeCode, $optionId, $storeId);
        $eavValue = $collection->getValueFromMagentoEav($optionId, $storeId);

        /** @var OptionSettingInterface|AbstractModel $model */
        $model = $collection->getFirstItem();
        if ($storeId !== \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $defaultModel = $collection->getLastItem();
            foreach ($model->getData() as $key => $value) {
                switch ($key) {
                    case OptionSettingInterface::META_TITLE:
                    case OptionSettingInterface::TITLE:
                        $isDefault = !$value || $eavValue === $value;
                        if ($isDefault) {
                            $model->setData($key, $defaultModel->getData($key) ?: $eavValue);
                        }
                        break;
                    case OptionSettingInterface::URL_ALIAS:
                        $isDefault = $value === null;
                        break;
                    default:
                        $isDefault = $defaultModel->getData($key) === $value;
                }

                $model->setData($key . '_use_default', $isDefault);
            }
        } else {
            $this->resolveTitleUseDefault($model, OptionSettingInterface::TITLE, $eavValue);
            $this->resolveTitleUseDefault($model, OptionSettingInterface::META_TITLE, $eavValue);
        }

        return $model;
    }

    /**
     * @param OptionSettingInterface $optionSetting
     * @return $this
     */
    public function save(OptionSettingInterface $optionSetting)
    {
        $this->resource->save($optionSetting);
        return $this;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getAllFeaturedOptionsArray($storeId): array
    {
        return $this->resource->getAllFeaturedOptionsArray($storeId);
    }

    public function deleteByOptionId(int $optionId): void
    {
        try {
            $table = $this->resource->getTable(OptionSettingRepositoryInterface::TABLE);
            $this->resource->getConnection()->delete($table, ['value = ?' => $optionId]);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to delete option with ID %1. Error: %2',
                    [$optionId, $e->getMessage()]
                )
            );
        }
    }

    /**
     * @param OptionSettingInterface $model
     * @param string $key
     * @param mixed $eavValue
     * @return void
     */
    private function resolveTitleUseDefault(OptionSettingInterface $model, string $key, $eavValue): void
    {
        $useDefaultKey = $key . '_use_default';
        $model->setData($useDefaultKey, false);
        $value = $model->getData($key);
        if (!$value || $model->getData($key) === $eavValue) {
            $model->setData($useDefaultKey, true);
            $model->setData($key, $eavValue);
        }
    }
}
