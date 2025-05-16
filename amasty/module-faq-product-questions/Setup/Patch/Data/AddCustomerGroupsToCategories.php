<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Base\Utils\BatchLoader;
use Amasty\Faq\Api\CategoryRepositoryInterface;
use Amasty\Faq\Model\ResourceModel\Category\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class AddCustomerGroupsToCategories implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var BatchLoader
     */
    private $batchLoader;

    /**
     * @var CustomerGroup
     */
    private $customerGroups;

    /**
     * @var CollectionFactory
     */
    private $categoryCollection;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        BatchLoader $batchLoader,
        CustomerGroup $customerGroups,
        CollectionFactory $collectionFactory,
        State $appState
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->batchLoader = $batchLoader;
        $this->customerGroups = $customerGroups->toOptionArray();
        $this->categoryCollection = $collectionFactory->create();
        $this->appState = $appState;
    }

    public function apply(): DataPatchInterface
    {
        $this->appState->emulateAreaCode(
            Area::AREA_GLOBAL,
            [$this, 'setCustomerGroups'],
            []
        );

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function setCustomerGroups(): void
    {
        try {
            $groups = implode(
                ',',
                array_map(function ($customerGroup) {
                    return $customerGroup['value'];
                }, $this->customerGroups)
            );

            foreach ($this->batchLoader->load($this->categoryCollection) as $category) {
                $category = $this->categoryRepository->getById($category->getId());

                if (empty($category->getCustomerGroups())) {
                    $category->setCustomerGroups($groups);
                    $this->categoryRepository->save($category);
                }
            }
        } catch (NoSuchEntityException $e) {
            null;
        }
    }
}
