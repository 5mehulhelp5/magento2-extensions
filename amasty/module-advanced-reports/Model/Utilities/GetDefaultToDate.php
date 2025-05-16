<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Model\Utilities;

use Magento\Backend\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FlagFactory;
use Magento\Framework\Flag;
use Magento\Framework\Stdlib\DateTime\DateTime;

class GetDefaultToDate
{
    public const DATE_TO_FLAG = 'amasty_reports_to_date';

    public const SESSION_KEY = 'am_reports_to';

    /**
     * @var FlagFactory
     */
    private $flagFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        FlagFactory $flagFactory,
        Session $session = null, // TODO move to not optional
        DateTime $dateTime = null // TODO move to not optional
    ) {
        $this->flagFactory = $flagFactory;
        $this->session = $session ?? ObjectManager::getInstance()->get(Session::class);
        $this->dateTime = $dateTime ?? ObjectManager::getInstance()->get(DateTime::class);
    }

    /**
     * @return int timestamp
     */
    public function execute(): int
    {
        try {
            $date = $this->session->getData(self::SESSION_KEY, false);
            if (!$date) {
                $date = $this->getFlag(self::DATE_TO_FLAG)->loadSelf()->getFlagData() ? : time();
            }
        } catch (LocalizedException $e) {
            $date = 0;
        }

        return (int) $date;
    }

    public function getDate(): string
    {
        return $this->dateTime->gmtDate('Y-m-d', $this->execute());
    }

    public function setDefaultValue(string $date): void
    {
        $this->session->setAmReportsTo(strtotime($date));
    }

    private function getFlag($code): Flag
    {
        return $this->flagFactory->create([
            'data' => [
                'flag_code' => $code
            ]
        ]);
    }
}
