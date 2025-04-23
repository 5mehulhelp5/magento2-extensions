<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Ui\Component\Listing\Columns;

use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Olegnax\InstagramFeedPro\Model\Data\IntsUser;

class IntsUserExpire extends Column
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * IntsUserExpire constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (
                    !empty($item[IntsUser::EXPIRE])
                    && -1 != $item[IntsUser::EXPIRE]
                ) {
                    $expiryDate = $this->timezone->date($item[IntsUser::EXPIRE]);

                    // Get the current date as a DateTime object
                    $currentDate = $this->timezone->date();

                    // Calculate the difference between the dates
                    $interval = $currentDate->diff($expiryDate);

                    // Check if it will expire in less than 5 days
                    $formatedExpiryDate = $expiryDate->format(DateTime::DATETIME_PHP_FORMAT);

                    $link = '';
                    if(IntsUser::ACCOUNT_TYPE_BUSINESS === $item[IntsUser::ACCOUNT_TYPE]){
                        $link = '<a href="https://olegnax.com/documentation/instagram-feed-pro-extension-for-magento-2/users/#extend-token" target="_blank">' . __('Extend Token'). '</a>';
                    }

                    if($interval->invert === 1){
                        $item[IntsUser::EXPIRE] = '<span class="ox-inst__status-label error">' . __('Expired') . '</span>' . $link ;
                    } elseif ($interval->invert === 0 && $interval->days < 5) {
                        $item[IntsUser::EXPIRE] = '<span class="ox-inst__status-label warn">' . $formatedExpiryDate . '</span>' . $link ;
                    } else {
                        $item[IntsUser::EXPIRE] = '<span class="ox-inst__status-label valid">' . $formatedExpiryDate . '</span>';
                    }

                } else {
                    $item[IntsUser::EXPIRE] = '';
                }
            }
        }

        return parent::prepareDataSource($dataSource);
    }
}