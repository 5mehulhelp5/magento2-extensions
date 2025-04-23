<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Api;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\PageFactory;
use Olegnax\InstagramFeedPro\Helper\Helper;
use Olegnax\InstagramFeedPro\Model\IntsUser\Facebook\AccountProcessor;
use Psr\Log\LoggerInterface;

class OauthGraph extends Action
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var LayoutInterface
     */
    protected $layout;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var AccountProcessor
     */
    protected $accountProcessor;
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Helper $helper
     * @param LayoutInterface $layout
     * @param AccountProcessor $accountProcessor
     * @param PageFactory $resultPageFactory
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Helper $helper,
        LayoutInterface $layout,
        AccountProcessor $accountProcessor,
        PageFactory $resultPageFactory,
        Json $json = null
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->layout = $layout;
        $this->accountProcessor = $accountProcessor;
        $this->resultPageFactory = $resultPageFactory;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $result = [];

        $error = (int)$this->getRequest()->getParam("error", 0);
        if (0 < $error) {
            $message = $this->getRequest()->getParam("message");
            if (!empty($message)) {
                $this->createError($message);
            }
        } else {
            $token = $this->helper->generateToken();
            $data = $this->getRequest()->getParam("data");

            if (empty($data)) {
                $message = $this->getRequest()->getParam("message");
                if (!empty($message) && is_string($message)) {
                    $this->createError(__($message));
                } else {
                    $this->createError(__('Received an empty token!'));
                }
            } else {
                try {
                    $_data1 = base64_decode($data);
                    $_data2 = str_replace($token, '', (string)$_data1);
                    $_data3 = base64_decode($_data2);
                    $result = $this->json->unserialize($_data3);
                    if (empty($result)) {
                        $this->createError(__('Error converting token!'));
                    } elseif (isset($result['time']) && 0 < $result['time']) {
                        if (0 < $result['expire']) {
                            $result['expire'] -= $result['time'] - time() + 24 * 60 * 60;
                        }
                        unset($result['time']);
                        $accounts_response = $this->accountProcessor->getAccounts($result);
                        if ($accounts_response && is_array($accounts_response) && array_key_exists('error', $accounts_response)) {
                            $this->createError($accounts_response['error']);
                        }
                    } else {
                        $this->createError(__('Token save error!'));
                    }
                } catch (Exception $e) {
                    $this->createError($e->getMessage());
                }
            }
        }

        return $this->resultFactory
            ->create(ResultFactory::TYPE_RAW)
            ->setContents($this->getContents($result))
            ->setHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0',
                true
            );
    }

    /**
     * @param $message
     */
    protected function createError($message)
    {
        $this->logger->debug("Instagram: " . $message);
        $this->messages[] = $message;
    }

    /**
     * @param $result
     *
     * @return string
     */
    protected function getContents($result)
    {
        ob_start(); ?>
		<script>
            (function (parent, messages, data) {
                window.opener.console.warn(parent, messages, data);

                if (0 < messages.length) {
                    let insertAfter = function (referenceNode, newNode) {
                            referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
                        },
                        createError = function (message) {
                            let div = parent.createElement("div"),
                                div2 = parent.createElement("div");
                            div.classList.add('message');
                            div.classList.add('message-error');
                            div.classList.add('error');
                            div2.setAttribute('data-ui-id', 'messages-message-error');
                            div2.innerHTML = message;
                            div.append(div2);
                            return div;
                        },
                        elMessages = parent.querySelector('#anchor-content #messages .messages');
                    if (!elMessages) {
                        let div = parent.createElement("div"),
                            div2 = parent.createElement("div");
                        div.id = 'messages';
                        div2.classList.add('messages');
                        div.append(div2);
                        insertAfter(parent.querySelector('#anchor-content .page-main-actions'), div);
                        elMessages = parent.querySelector('#anchor-content #messages .messages');
                    }
                    elMessages.innerHTML = '';
                    for (let i = 0; i < messages.length; i++) {
                        elMessages.append(createError(messages[i]));
                    }
                } else {
                    window.opener.location.reload();
                }
            })(
                window.opener.document,
                <?= $this->json->serialize($this->messages) ?>,
                <?= $this->json->serialize($result) ?>
            );
            window.close();
		</script>
        <?php
        return ob_get_clean();
    }
}
