<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Helper;

use Amasty\Reports\Model\Store;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    private const DATE_FROM_FLAG = 'amasty_reports_from_date';
    private const DATE_TO_FLAG = 'amasty_reports_to_date';

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $priceCurrency;

    /**
     * @var \Magento\Framework\FlagFactory
     */
    private $flagFactory;

    /**
     * @var \Amasty\Reports\Model\Source\Country
     */
    private $sourceContry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Magento\Framework\Intl\DateTimeFactory
     */
    private $dateTimeFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\FlagFactory $flagFactory,
        \Magento\Framework\Intl\DateTimeFactory $dateTimeFactory,
        \Amasty\Reports\Model\Source\Country $sourceContry
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->flagFactory = $flagFactory;
        $this->sourceContry = $sourceContry;
        $this->localeDate = $localeDate;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @deprecated
     * @see \Amasty\Reports\Model\Utilities\GetDefaultFromDate::execute
     * @return int
     */
    public function getDefaultFromDate()
    {
        return ObjectManager::getInstance()->get(\Amasty\Reports\Model\Utilities\GetDefaultFromDate::class)->execute();
    }

    /**
     * @deprecated
     * @see \Amasty\Reports\Model\Utilities\GetDefaultToDate::execute
     * @return int
     */
    public function getDefaultToDate()
    {
        return ObjectManager::getInstance()->get(\Amasty\Reports\Model\Utilities\GetDefaultToDate::class)->execute();
    }

    /**
     * @return int
     * @deprecated
     * @see \Amasty\Reports\Model\Store::getCurrentStoreId
     */
    public function getCurrentStoreId()
    {
        return ObjectManager::getInstance()->get(Store::class)->getCurrentStoreId();
    }

    /**
     * Getting time according to locale
     * We are using reversed timezone offset to retrieve correct data from database with UTC-0 timezone
     *
     * @param string|\DateTime $date
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return string
     */
    public function getDateForLocale($date, $hour = 0, $minute = 0, $second = 0)
    {
        $skipTimeZoneConversion = $this->scopeConfig->getValue(
            'config/skipTimeZoneConversion',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $date = $this->dateTimeFactory->create($date)->setTimezone(new \DateTimeZone('UTC'));
        if ($skipTimeZoneConversion) {
            $date = $this->localeDate->date($date);
        }
        $date->setTime($hour, $minute, $second);
        $timestampWithReversedOffset = $date->getTimestamp() - $this->getTimezoneOffset();

        return $date->setTimestamp($timestampWithReversedOffset)
            ->format('Y-m-d H:i:s');
    }

    /**
     * @param bool $inSeconds
     * @return int|string
     */
    public function getTimezoneOffset($inSeconds = true)
    {
        $skipTimeZoneConversion = $this->scopeConfig->getValue(
            'config/skipTimeZoneConversion',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $date = $skipTimeZoneConversion
            ? $this->dateTimeFactory->create()->setTimezone(new \DateTimeZone('UTC'))
            : $this->localeDate->date();

        return $inSeconds ? $date->getOffset() : $date->format('P');
    }

    /**
     * @return string
     */
    public function getStatuses(): string
    {
        return (string) $this->scopeConfig->getValue('amasty_reports/general/reports_statuses');
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function getCurrencyCode()
    {
        $params = $this->_request->getParam('amreports', []);
        $storeId = isset($params['store_id']) ? $params['store_id'] : \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $store = $this->storeManager->getStore($storeId);

        return $store->getBaseCurrencyCode();
    }

    /**
     * @param $price
     * @return float|string
     */
    public function convertPrice($price)
    {
        return $this->priceCurrency->convertAndFormat(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getCurrentStoreId(),
            $this->getDisplayCurrency()
        );
    }

    /**
     * @return string
     */
    public function getDisplayCurrency()
    {
        return $this->scopeConfig->getValue(
            Currency::XML_PATH_CURRENCY_DEFAULT,
            ScopeInterface::SCOPE_STORE,
            $this->getCurrentStoreId()
        );
    }

    /**
     * @return \Amasty\Reports\Model\Source\Country
     */
    public function getCountryDataSource()
    {
        return $this->sourceContry;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }
}
