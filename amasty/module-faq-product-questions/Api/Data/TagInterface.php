<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Api\Data;

interface TagInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const TAG_ID = 'tag_id';
    public const TITLE = 'title';
    /**#@-*/

    /**
     * @return int
     */
    public function getTagId();

    /**
     * @param int $tagId
     *
     * @return \Amasty\Faq\Api\Data\TagInterface
     */
    public function setTagId($tagId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return \Amasty\Faq\Api\Data\TagInterface
     */
    public function setTitle($title);
}
