<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Model\Label\Save;

class DataPreprocessorCombine implements DataPreprocessorInterface
{
    public const SORT_ORDER = 'sortOrder';
    public const PREPROCESSOR = 'preprocessor';

    /**
     * @var array[]
     *
     * @example [
     *      [
     *          'sortOrder' => 5,
     *          'preprocessor' => $preprocessorInstance
     *      ]
     * ]
     */
    private $preprocessors;

    public function __construct(
        $preprocessors = []
    ) {
        $this->preprocessors = $this->sortConfigs($preprocessors);
    }

    /**
     * @return DataPreprocessorInterface[]
     */
    private function getPreprocessors(): array
    {
        $preprocessors = [];

        foreach ($this->preprocessors as $preprocessorConfig) {
            $preprocessor = $preprocessorConfig[self::PREPROCESSOR] ?? null;

            if ($preprocessor instanceof DataPreprocessorInterface) {
                $preprocessors[] = $preprocessor;
            }
        }

        return $preprocessors;
    }

    public function process(array $data): array
    {
        /** @var DataPreprocessorInterface $preprocessor **/
        foreach ($this->getPreprocessors() as $preprocessor) {
            $data = $preprocessor->process($data);
        }

        return $data;
    }

    private function sortConfigs($configs): array
    {
        usort($configs, function (array $configA, array $configB) {
            $sortOrderA = $configA[self::SORT_ORDER] ?? 0;
            $sortOrderB = $configB[self::SORT_ORDER] ?? 0;

            return $sortOrderA <=> $sortOrderB;
        });

        return $configs;
    }
}
