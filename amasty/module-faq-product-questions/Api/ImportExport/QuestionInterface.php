<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api\ImportExport;

interface QuestionInterface extends \Amasty\Faq\Api\Data\QuestionInterface
{
    public const QUESTION = 'question';

    public const STORE_CODES = 'store_codes';

    public const PRODUCT_SKUS = 'product_skus';

    public const CATEGORY_IDS = 'category_ids';
}
