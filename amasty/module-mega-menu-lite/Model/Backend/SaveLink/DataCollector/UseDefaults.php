<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Core Base for Magento 2
 */

namespace Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollector;

use Amasty\MegaMenuLite\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenuLite\Model\Backend\SaveLink\DataCollectorInterface;
use Amasty\MegaMenuLite\Model\Provider\FieldsByStore;
use Magento\Framework\DataObject;

class UseDefaults implements DataCollectorInterface
{
    /**
     * @var FieldsByStore
     */
    private $uiFieldsByStore;

    /**
     * @var DataObject
     */
    private $uiScopeProvider;

    public function __construct(
        FieldsByStore $uiFieldsByStore,
        DataObject $uiScopeProvider
    ) {
        $this->uiFieldsByStore = $uiFieldsByStore;
        $this->uiScopeProvider = $uiScopeProvider;
    }

    public function execute(array $data): array
    {
        if (!$data[ItemInterface::STORE_ID]) {
            return $data;
        }

        $useDefaults = $data[ItemInterface::USE_DEFAULT] ?? [];
        foreach ($this->uiFieldsByStore->getCustomFields() as $fieldSet) {
            foreach ($fieldSet as $field) {
                $fieldScope = $this->uiScopeProvider->getData($field) ?? $field;
                if ($fieldScope !== $field && isset($useDefaults[$fieldScope]) && $useDefaults[$fieldScope]) {
                    unset($useDefaults[$field]);
                    continue;
                }

                if (!empty($useDefaults[$field])) {
                    $data[$fieldScope] = null;
                    $useDefaults[$fieldScope] = '1';
                } else {
                    $data[$fieldScope] = $data[$fieldScope] ?? null;
                    $useDefaults[$fieldScope] = '0';
                }

                if ($fieldScope !== $field) {
                    unset($useDefaults[$field]);
                }
            }
        }

        $useDefaults = array_keys(array_filter($useDefaults));
        $useDefaults = implode(ItemInterface::SEPARATOR, $useDefaults);
        $data[ItemInterface::USE_DEFAULT] = $useDefaults;

        return $data;
    }
}
