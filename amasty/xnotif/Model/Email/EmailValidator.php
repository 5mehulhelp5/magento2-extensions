<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Email;

use Laminas\Validator\EmailAddress;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;

class EmailValidator
{
    /**
     * @var EmailAddress
     */
    private $emailAddressValidator;

    public function __construct(
        EmailAddress $emailAddressValidator = null // TODO move to not optional
    ) {
        $this->emailAddressValidator = $emailAddressValidator ?? ObjectManager::getInstance()->get(EmailAddress::class);
    }

    /**
     * @param string $email
     *
     * @return string
     * @throws LocalizedException
     */
    public function execute(string $email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!$this->emailAddressValidator->isValid($email)) {
            throw new LocalizedException(__('Please enter a valid email address.'));
        }

        return $email;
    }
}
