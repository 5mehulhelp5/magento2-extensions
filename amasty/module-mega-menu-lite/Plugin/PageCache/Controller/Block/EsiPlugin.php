<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Plugin\PageCache\Controller\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\PageCache\Controller\Block\Esi;

class EsiPlugin
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        MessageManagerInterface $messageManager,
        LayerResolver $layerResolver
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->messageManager = $messageManager;
        $this->layerResolver = $layerResolver;
    }

    public function beforeExecute(Esi $subject): array
    {
        $categoryId = (int) $subject->getRequest()->getParam('current_category', 0);
        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $this->layerResolver->get()->setCurrentCategory($category);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This Entity no longer exists.'));
            }
        }

        return [];
    }
}
