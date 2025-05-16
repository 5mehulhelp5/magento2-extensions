<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Product Reviews Base for Magento 2
 */

namespace Amasty\AdvancedReview\Model;

use Amasty\AdvancedReview\Api\Data\UnsubscribeInterface;
use Magento\Framework\Model\AbstractModel;

class Unsubscribe extends AbstractModel implements UnsubscribeInterface
{
    public function _construct()
    {
        $this->_init(\Amasty\AdvancedReview\Model\ResourceModel\Unsubscribe::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(Unsubscribe::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(Unsubscribe::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnsubscribedAt()
    {
        return $this->_getData(Unsubscribe::UNSUBSCRIBED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUnsubscribedAt($unsubscribedAt)
    {
        $this->setData(Unsubscribe::UNSUBSCRIBED_AT, $unsubscribedAt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->_getData(Unsubscribe::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        $this->setData(Unsubscribe::EMAIL, $email);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsComment()
    {
        return $this->_getData(Unsubscribe::IS_COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setIsComment($isComment)
    {
        $this->setData(Unsubscribe::IS_COMMENT, $isComment);

        return $this;
    }
}
