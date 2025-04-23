<?php
declare(strict_types=1);

namespace MageWorx\Checkout\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * For guest checkout quote id must be always masked
 */
class FixQuoteIdForGuest
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @param CheckoutSession $checkoutSession
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->checkoutSession          = $checkoutSession;
        $this->quoteIdMaskFactory       = $quoteIdMaskFactory;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $config
     * @return array
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $config): array {
        try {
            $config['quoteData']['entity_id'] = $this->getQuoteId();
        } catch (LocalizedException $localizedException) {
            return $config;
        }

        return $config;
    }

    /**
     * Use masked quote id for guest customer
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getQuoteId(): string
    {
        $quote   = $this->checkoutSession->getQuote();
        $quoteId = $quote->getId();
        if ($quoteId && !$quote->getCustomerId()) {
            /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $quoteId     = $quoteIdMask->load(
                $quoteId,
                'quote_id'
            )->getMaskedId();
        }

        return (string)$quoteId;
    }
}
