<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\System\Config\Form;


use IntlDateFormatter;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Cron\Model\Schedule;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CronInfo extends Field
{
    const ACTION = 'olegnax_instagramfeedpro_updateposts';
    /**
     * @var CollectionFactory
     */
    protected $_cronColletion;

    /**
     * CronInfo constructor.
     * @param Context $context
     * @param CollectionFactory $cronColletion
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $cronColletion,
        array $data = []
    ) {
        $this->_cronColletion = $cronColletion;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {

        $html = $this->addMessage();
		$cronLabel = '';
		if(static::ACTION == 'olegnax_instagramfeedpro_updateposts'){
			$cronLabel = 'Cron Update Posts Status';
		}
		if(static::ACTION == 'olegnax_instagramfeedpro_newposts'){
			$cronLabel = 'Cron New Posts Status';
		}
        return $this->_decorateRowHtml($element, '<td colspan="2"><h2 style="font-weight:600;margin-bottom: 0;  margin-top: 10px;">' . $cronLabel . '</h2>' . $html . '</td>');
    }

    /**
     * @return string
     */
    private function addMessage()
    {
        if (!$this->isCronEnabled()) {
            return '<span class="ox-cron-status -error">' . __('Cron is disabled! Automatic post updates will not run.') . '</span>';
        }
        $statuses = $this->getStatuses();
        if (is_array($statuses)) {
            $message = [];
            /** @var Schedule $schedule */
            foreach ($statuses as $status => $schedule) {
                switch ($status) {
                    case Schedule::STATUS_PENDING:
                    case Schedule::STATUS_RUNNING:
                        $message[] = '<span class="ox-cron-status -success">' . __(
                            'Next Cron Job is scheduled for %1',
                            $this->formatDate(
                                $schedule->getScheduledAt(),
                                IntlDateFormatter::SHORT,
                                true
                            )
                        ) . '</span>';
                        break;
                    case Schedule::STATUS_MISSED:
                    case Schedule::STATUS_ERROR:
                        $message[] = '<span class="ox-cron-status -error">' . $schedule->getMessages() . '</span>';
                        break;
                    case Schedule::STATUS_SUCCESS:
                        $message[] = '<span class="ox-cron-status -success">' .  __(
                            'Cron: Last successful execution %1',
                            $this->formatDate(
                                $schedule->getExecutedAt(),
                                IntlDateFormatter::SHORT,
                                true
                            )
                        ) . '</span>';
                        break;
                }
            }
            return implode(' ', $message);
        }

        return '<span class="ox-cron-status" style="background: #fffacb;">' . __('Cron Job has not yet been scheduled to run.') . '</span>';
    }

    /**
     * @return bool
     */
    private function isCronEnabled()
    {
        return 0 < $this->_cronColletion->create()
                ->addFieldToSelect('schedule_id')
                ->count();
    }

    /**
     * @return Schedule[]|array|bool
     */
    private function getStatuses()
    {
        $collectionSchedule = $this->_cronColletion->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('job_code', static::ACTION)
            ->addOrder('scheduled_at');
        if (0 == $collectionSchedule->count()) {
            return false;
        }
        $result = [];
        /** @var Schedule $schedule */
        foreach ($collectionSchedule as $schedule) {
            $status = $schedule->getStatus();
            if (!array_key_exists($status, $result)) {
                $result[$status] = $schedule;
                if (Schedule::STATUS_SUCCESS === $status) {
                    break;
                }
            }
        }
        return $result;
    }
}
