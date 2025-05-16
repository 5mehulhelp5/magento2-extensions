<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Exceptions;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class VotingNotAllowedException extends LocalizedException
{
    /**
     * @var string
     */
    private $msgCode;

    public function __construct(Phrase $phrase = null, string $msgCode = '', \Exception $cause = null, $code = 0)
    {
        if (!$phrase) {
            $phrase = __('Voting is not allowed.');
        }
        $this->msgCode = $msgCode;
        parent::__construct($phrase, $cause, (int) $code);
    }

    public function getMessageCode(): string
    {
        return $this->msgCode;
    }
}
