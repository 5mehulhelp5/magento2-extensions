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


namespace Mirasvit\Helpdesk\Model\Mail\Template;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder implements TransportBuilderInterface
{
    /**
     * @var mixed
     */
    private $addressConverter;
    /**
     * @var mixed
     */
    private $emailMessageInterfaceFactory;
    /**
     * @var mixed
     */
    private $mimeMessageInterfaceFactory;
    /**
     * @var mixed
     */
    private $mimePartInterfaceFactory;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var array
     */
    protected $mstCustomHeaders = [];
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * TransportBuilder constructor.
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param ProductMetadataInterface $productMetadata
     * @param Manager $moduleManager
     * @param MessageInterfaceFactory|null $messageFactory
     * @param null $emailMessageInterfaceFactory
     * @param null $mimeMessageInterfaceFactory
     * @param null $mimePartInterfaceFactory
     * @param null $addressConverter
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory,
        ProductMetadataInterface $productMetadata,
        Manager $moduleManager,
        MessageInterfaceFactory $messageFactory = null,
        $emailMessageInterfaceFactory = null,
        $mimeMessageInterfaceFactory = null,
        $mimePartInterfaceFactory = null,
        $addressConverter = null
    ) {
        $this->productMetadata = $productMetadata;
        $this->moduleManager = $moduleManager;

        if ($this->isBelow233()) {
            parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory);
        } else {
            parent::__construct($templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory,
                $messageFactory, $emailMessageInterfaceFactory, $mimeMessageInterfaceFactory, $mimePartInterfaceFactory,
                $addressConverter);
            $this->emailMessageInterfaceFactory = $emailMessageInterfaceFactory ?: $this->objectManager
                ->get(\Magento\Framework\Mail\EmailMessageInterfaceFactory::class);
            $this->mimeMessageInterfaceFactory = $mimeMessageInterfaceFactory ?: $this->objectManager
                ->get(\Magento\Framework\Mail\MimeMessageInterfaceFactory::class);
            $this->mimePartInterfaceFactory = $mimePartInterfaceFactory ?: $this->objectManager
                ->get(\Magento\Framework\Mail\MimePartInterfaceFactory::class);
            $this->addressConverter = $addressConverter ?: $this->objectManager
                ->get(\Magento\Framework\Mail\AddressConverter::class);
        }

        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        parent::reset();
        $this->attachments = [];
        $this->mstCustomHeaders = [];

        return $this;
    }

    /**
     * to fix phpstan
     * @return \Zend_Mail
     */
    private function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttachment(
        $body,
        $mimeType = \Magento\Framework\HTTP\Mime::TYPE_OCTETSTREAM,
        $disposition = \Magento\Framework\HTTP\Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Magento\Framework\HTTP\Mime::ENCODING_BASE64,
        $filename = null
    ) {

        if ($this->hasBuiltInAttachmentFunction()) {
            return $this->getMessage()->createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        }

        if ($body instanceof \Fooman\EmailAttachments\Model\Api\AttachmentInterface &&
            $this->moduleManager->isEnabled('Fooman_EmailAttachments')
        ) {
            $mimeType    = $body->getMimeType();
            $disposition = $body->getDisposition();
            $encoding    = $body->getEncoding();
            $filename    = $body->getFilename();
            $body        = $body->getContent();
        }

        if (version_compare(CompatibilityService::getVersion(), "2.4.3-p3", ">=")) {
            $attach = new \Laminas\Mime\Part($body);
        } else {
            $attach = new \Zend\Mime\Part($body);
        }

        $attach->setType($mimeType);
        $attach->setDisposition($disposition);
        $attach->setEncoding($encoding);
        $attach->setFileName($filename);

        $this->attachments[] = $attach;
        return $this;
    }

    /**
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();

        if ($this->hasBuiltInAttachmentFunction()) {
            return $this;
        }

        $laminasExists = (version_compare(CompatibilityService::getVersion(), "2.3.5", ">"));

        if (!count($this->mstCustomHeaders) && (!count($this->attachments))) {
            return $this;
        }

        if ($this->getMessage() instanceof \Ebizmarts\Mandrill\Model\Message) {
            /** @var \Zend\Mime\Part $attachment */
            foreach ($this->attachments as $attachment) {
                $this->getMessage()->createAttachment(
                    base64_decode($attachment->getContent()),
                    $attachment->getType(),
                    $attachment->getDescription(),
                    $attachment->getEncoding(),
                    $attachment->getFileName()
                );
            }
        } elseif ($this->isBelow233()) {
            $parts = $this->getMessage()->getBody()->getParts();
            $parts = array_merge($parts, $this->attachments);
            $body  = new \Zend\Mime\Message();
            $body->setParts($parts);
            $this->getMessage()->setBody($body);
        } else {
            $parts = $this->getMessage()->getBody()->getParts();
            $parts = array_merge($parts, $this->attachments);

            $messageData = [
                'encoding' => $this->getMessage()->getEncoding(),
                'subject'  => $this->getMessage()->getSubject(),
                'sender'   => $this->getMessage()->getSender(),
                'to'       => $this->getMessage()->getTo(),
                'replyTo'  => $this->getMessage()->getReplyTo(),
                'from'     => $this->getMessage()->getFrom(),
                'cc'       => $this->getMessage()->getCc(),
                'bcc'      => $this->getMessage()->getBcc(),
            ];
            $messageData['body'] = $this->mimeMessageInterfaceFactory->create(
                ['parts' => $parts]
            );

            if ($laminasExists) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->message = $objectManager->create(\Mirasvit\Helpdesk\Model\Mail\HelpdeskCustomMessage::class, $messageData);
                foreach ($this->mstCustomHeaders as $key => $value) {
                    $this->message->addHeader($key,  $value, true);
                }
            } else {
                $this->message = $this->emailMessageInterfaceFactory->create($messageData);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function hasBuiltInAttachmentFunction()
    {
        return version_compare($this->productMetadata->getVersion(), "2.2.8", "<");
    }

    /**
     * @return bool
     */
    protected function isBelow233()
    {
        return version_compare($this->productMetadata->getVersion(), "2.3.3", "<");
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomHeader($mstCustomHeaders)
    {
        $this->mstCustomHeaders = $mstCustomHeaders;
    }
}
