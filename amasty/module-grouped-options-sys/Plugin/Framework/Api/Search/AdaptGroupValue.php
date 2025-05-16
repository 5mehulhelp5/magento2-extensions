<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Plugin\Framework\Api\Search;

use Amasty\GroupedOptions\Model\GroupAttr\GetFakeKeyByCode;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AdaptGroupValue
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetFakeKeyByCode
     */
    private $getFakeKeyByCode;

    public function __construct(
        GetFakeKeyByCode $getFakeKeyByCode,
        AttributeRepositoryInterface $attributeRepository,
        RequestInterface $request
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->request = $request;
        $this->getFakeKeyByCode = $getFakeKeyByCode;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSearch(SearchInterface $subject, SearchCriteriaInterface $searchCriteria): array
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                try {
                    $attribute = $this->attributeRepository->get(
                        Product::ENTITY,
                        $filter->getField()
                    );
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                if ($attribute->getBackendType() === 'int' && is_numeric($filter->getValue())) {
                    $attributeValues = $this->request->getParam(
                        $filter->getField()
                    ); // try load from request; magento convert to int
                    if ($attributeValues === null) {
                        continue;
                    }
                    $attributeValues = explode(',', $attributeValues);
                } else {
                    $attributeValues = $filter->getValue();
                    if (!is_array($attributeValues)) {
                        $attributeValues = [$attributeValues];
                    }
                }

                foreach ($attributeValues as $key => $attributeValue) {
                    if (is_string($attributeValue)) {
                        $attributeValue = $this->getFakeKeyByCode->execute(
                            (int) $attribute->getAttributeId(),
                            $attributeValue
                        );

                        if ($attributeValue) {
                            $attributeValues[$key] = $attributeValue;
                            $filter->setValue($attributeValues);
                        }
                    }
                }
            }
        }

        return [$searchCriteria];
    }
}
