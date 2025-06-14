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



namespace Mirasvit\Helpdesk\Helper;

/**
 * @SuppressWarnings(PHPMD)
 */
class Help extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @var array
     */
    protected $help = [
        'system' => [
            'general_default_priority' => '',
            'general_default_status' => '',
            'general_sign_ticket_by' => '',
            'general_archived_status_list' => '',
            'general_locked_status_list' => '',
            'general_contact_us_is_active' => '',
            'general_bcc_email' => '',
            'general_is_wysiwyg' => '',
            'general_is_default_cron' => '',
            'contact_form_is_active' => '',
            'contact_form_default_department' => '',
            'contact_form_color' => '',
            'contact_form_title' => '',
            'contact_form_position' => '',
            'contact_form_form_title' => '',
            'contact_form_subject_title' => '',
            'contact_form_subject_placeholder' => '',
            'contact_form_description_title' => '',
            'contact_form_description_placeholder' => '',
            'contact_form_is_active_attachment' => '',
            'contact_form_is_allow_priority' => '',
            'contact_form_is_allow_department' => '',
            'contact_form_is_active_kb' => '',
            'notification_is_show_code' => '',
            'notification_history_records_number' => '',
            'notification_notification_template_new_ticket_template' => '',
            'notification_notification_template_staff_new_ticket_template' => '',
            'notification_notification_template_new_message_template' => '',
            'notification_notification_template_staff_new_message_template' => '',
            'notification_notification_template_third_new_message_template' => '',
            'notification_notification_template_reminder_template' => '',
            'notification_notification_template_rule_template' => '',
            'notification_notification_template_staff_new_satisfaction_template' => '',
            'satisfaction_is_active' => '',
            'satisfaction_is_show_results_in_ticket' => '',
            'satisfaction_is_send_results_owner' => '',
            'satisfaction_results_email' => '',
            'frontend_is_active' => '',
            'frontend_is_allow_priority' => '',
            'frontend_is_allow_department' => '',
            'frontend_is_allow_order' => '',
            'developer_is_active' => '',
            'developer_sandbox_email' => '',
        ],
    ];

    /************************/
}
