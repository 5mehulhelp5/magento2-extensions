<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api\ImportExport;

interface CategoryInterface extends \Amasty\Faq\Api\Data\CategoryInterface
{
    public const STORE_CODES = 'store_codes';

    public const QUESTION_IDS = 'question_ids';
}
