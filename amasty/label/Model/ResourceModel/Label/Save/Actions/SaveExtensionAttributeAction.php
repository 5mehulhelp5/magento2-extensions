<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\ResourceModel\Label\Save\Actions;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Parts\MetaProvider;
use Amasty\Label\Model\ResourceModel\Label\Save\AdditionalSaveActionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;

class SaveExtensionAttributeAction implements AdditionalSaveActionInterface
{
    /**
     * @var MetaProvider
     */
    private $metaProvider;

    /**
     * @var string
     */
    private $labelPartCode;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        MetaProvider $metaProvider,
        ResourceConnection $resourceConnection,
        string $labelPartCode
    ) {
        $this->metaProvider = $metaProvider;
        $this->labelPartCode = $labelPartCode;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(LabelInterface $label): void
    {
        $getter = $this->metaProvider->getGetter($this->labelPartCode);
        $extensionAttributes = $label->getExtensionAttributes()->{$getter}();

        if (is_array($extensionAttributes)) {
            foreach ($extensionAttributes as $extensionAttribute) {
                if (!$extensionAttribute instanceof DataObject) {
                    continue;
                }
                $this->saveExtensionAttribute($label, $extensionAttribute);
            }
        } elseif ($extensionAttributes instanceof DataObject) {
            $this->saveExtensionAttribute($label, $extensionAttributes);
        }
    }

    private function saveExtensionAttribute(LabelInterface $label, DataObject $extensionAttribute): void
    {
        $data = $extensionAttribute->getData();
        $data[LabelInterface::LABEL_ID] = $label->getLabelId();
        $connection = $this->resourceConnection->getConnection();
        $data = array_intersect_key($data, array_flip($this->getTableFields()));
        $connection->insertOnDuplicate(
            $this->resourceConnection->getTableName($this->metaProvider->getTable($this->labelPartCode)),
            $data
        );
    }

    /**
     * @return string[]
     */
    private function getTableFields(): array
    {
        $tableDescription = $this->resourceConnection->getConnection()->describeTable(
            $this->resourceConnection->getTableName($this->metaProvider->getTable($this->labelPartCode))
        );

        return array_keys($tableDescription);
    }
}
