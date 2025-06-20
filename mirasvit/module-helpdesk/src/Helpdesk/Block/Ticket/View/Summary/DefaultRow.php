<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Block\Ticket\View\Summary;

use Magento\Framework\View\Element\Template;
use Mirasvit\Helpdesk\Api\Data\TicketInterface;

class DefaultRow extends Template
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return __($this->getData('label'));
    }

    /**
     * @return TicketInterface
     */
    public function getItem()
    {
        return $this->getData('item');
    }

    /**
     * @return string
     */
    public function getColumn()
    {
        return $this->getNameInLayout();
    }
}