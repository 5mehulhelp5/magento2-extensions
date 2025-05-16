<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Grouped Options for Magento 2
 */

namespace Amasty\GroupedOptions\Ui\Button\Group;

class DeleteButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if ($groupId = $this->getGroupId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    'deleteConfirm("%s", "%s")',
                    __('Are you sure you want to delete this group?'),
                    $this->getUrl('*/*/delete', ['group_id' => $groupId])
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
