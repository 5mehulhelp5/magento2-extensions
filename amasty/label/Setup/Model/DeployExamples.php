<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Setup\Model;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class DeployExamples
{
    public const EXAMPLES_PATH = 'data/examples';
    public const STORES_KEY = 'stores';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ConvertFlatLabelDataToStructuredView
     */
    private $converter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Reader $reader,
        File $file,
        Serializer $serializer,
        ConvertFlatLabelDataToStructuredView $converter,
        StoreManagerInterface $storeManager
    ) {
        $this->reader = $reader;
        $this->file = $file;
        $this->serializer = $serializer;
        $this->converter = $converter;
        $this->storeManager = $storeManager;
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, int $firstPossibleId = 1): void
    {
        $examples = $this->getLabelExamples();
        $labelId = $firstPossibleId;
        $connection = $moduleDataSetup->getConnection();
        $storeIds = array_map(function (StoreInterface $store) {
            return $store->getId();
        }, $this->storeManager->getStores());

        foreach ($examples as $example) {
            $example[LabelInterface::LABEL_ID] = $labelId++;
            $example[self::STORES_KEY] = join(',', $storeIds);
            $labels = $this->converter->convert($example);

            foreach ($labels as $labelData) {
                foreach ($labelData as $tableName => $tableData) {
                    $tableName = $moduleDataSetup->getTable($tableName);

                    if (!empty($tableData)) {
                        $connection->insertOnDuplicate($tableName, $tableData);
                    }
                }
            }
        }
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    private function getLabelExamples(): array
    {
        $labelsDir = $this->reader->getModuleDir('', 'Amasty_Label');
        $examplePath = $labelsDir . DIRECTORY_SEPARATOR . self::EXAMPLES_PATH . DIRECTORY_SEPARATOR . $this->fileName;

        if (empty($this->fileName) || !$this->file->fileExists($examplePath)) {
            throw new LocalizedException(__('Label examples file name invalid'));
        }

        $jsonContent = $this->file->read($examplePath);

        try {
            $result = $this->serializer->unserialize($jsonContent);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Invalid examples content.'));
        }

        return $result;
    }
}
