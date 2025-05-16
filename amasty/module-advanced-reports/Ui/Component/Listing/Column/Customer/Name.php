<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Ui\Component\Listing\Column\Customer;

use Magento\Ui\Component\Listing\Columns\Column;

class Name extends Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['isTotal']) && !!$item['isTotal']) {
                    continue;
                }

                $item[$this->getData('name')] = empty($item[$this->getData('name')])
                    ? __('Guest')
                    : $item[$this->getData('name')];
            }
        }

        return $dataSource;
    }
}
