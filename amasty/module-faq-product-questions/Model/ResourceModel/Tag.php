<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\ResourceModel;

use Amasty\Faq\Api\Data\TagInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tag extends AbstractDb
{
    public const TABLE_NAME = 'amasty_faq_tag';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, TagInterface::TAG_ID);
    }

    /**
     * @param \Amasty\Faq\Api\Data\TagInterface[] $tags
     *
     * @return int[]
     */
    public function saveNoExistTags($tags)
    {
        $tagIds = [];
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (!$tag->getTagId()) {
                    $this->save($tag);
                }
                $tagIds[] = $tag->getTagId();
            }
        }

        return $tagIds;
    }
}
