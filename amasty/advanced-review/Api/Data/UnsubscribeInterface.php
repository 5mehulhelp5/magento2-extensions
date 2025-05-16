<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Api\Data;

interface UnsubscribeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const ENTITY_ID = 'entity_id';

    public const UNSUBSCRIBED_AT = 'unsubscribed_at';

    public const EMAIL = 'email';

    public const IS_COMMENT = 'isComment';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getUnsubscribedAt();

    /**
     * @param string $unsubscribedAt
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setUnsubscribedAt($unsubscribedAt);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setEmail($email);

    /**
     * @return boolean
     */
    public function getIsComment();

    /**
     * @param boolean $isComment
     *
     * @return \Amasty\AdvancedReview\Api\Data\UnsubscribeInterface
     */
    public function setIsComment($isComment);
}
