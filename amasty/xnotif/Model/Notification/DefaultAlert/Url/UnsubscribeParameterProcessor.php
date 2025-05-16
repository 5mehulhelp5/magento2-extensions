<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Out of Stock Notification for Magento 2
 */

namespace Amasty\Xnotif\Model\Notification\DefaultAlert\Url;

use LogicException;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\DecoderInterface as UrlDecoder;
use Magento\Framework\Url\EncoderInterface as UrlEncoder;

class UnsubscribeParameterProcessor
{
    private const CONDITIONS_KEY = 'conditions';
    private const ALLOWED_TYPES = ['email', 'customer_id'];

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var UrlEncoder
     */
    private $urlEncoder;

    /**
     * @var UrlDecoder
     */
    private $urlDecoder;

    public function __construct(
        EncryptorInterface $encryptor,
        Json $json,
        UrlEncoder $urlEncoder,
        UrlDecoder $urlDecoder
    ) {
        $this->encryptor = $encryptor;
        $this->json = $json;
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
    }

    public function encode(string $type, string $value): string
    {
        return $this->urlEncoder->encode($this->encryptor->encrypt($this->json->serialize([
            self::CONDITIONS_KEY => [$type => $value]
        ])));
    }

    public function decode(string $hash): array
    {
        $data = $this->json->unserialize($this->encryptor->decrypt($this->urlDecoder->decode($hash)));
        if (isset($data[self::CONDITIONS_KEY])
            && is_array($data[self::CONDITIONS_KEY])
            && count($data[self::CONDITIONS_KEY])
        ) {
            $data = $data[self::CONDITIONS_KEY];
            $type = array_key_first($data);
            if (!in_array($type, self::ALLOWED_TYPES, true)) {
                throw new LogicException('Invalid request data.');
            }

            return $data;
        }

        throw new LogicException('Invalid request data.');
    }
}
