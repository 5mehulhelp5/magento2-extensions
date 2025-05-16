<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Base\Utils\BatchLoader;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Model\ResourceModel\Question\CollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

class AddCustomerGroupsToQuestions implements DataPatchInterface, NonTransactionableInterface
{
    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

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
    private $questionCollection;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        QuestionRepositoryInterface $questionRepository,
        BatchLoader $batchLoader,
        CustomerGroup $customerGroups,
        CollectionFactory $collectionFactory,
        State $appState
    ) {
        $this->questionRepository = $questionRepository;
        $this->batchLoader = $batchLoader;
        $this->customerGroups = $customerGroups->toOptionArray();
        $this->questionCollection = $collectionFactory->create();
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

            foreach ($this->batchLoader->load($this->questionCollection) as $question) {
                $question = $this->questionRepository->getById($question->getId());

                if (empty($question->getCustomerGroups())) {
                    $question->setCustomerGroups($groups);
                    $this->questionRepository->save($question);
                }
            }
        } catch (NoSuchEntityException $e) {
            null;
        }
    }
}
