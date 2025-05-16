<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api\ImportExport;

interface ExportInterface
{
    public const QUESTION_EXPORT = 'faq_question_export';

    public const CATEGORY_EXPORT = 'faq_category_export';

    public const EXPORT_TYPES = [self::QUESTION_EXPORT, self::CATEGORY_EXPORT];

    public const BLOCK_NAME = 'faq.export';
}
