<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsUser;

use Exception;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Olegnax\InstagramFeedPro\Model\IntsUser\Facebook\AccountProcessor;
use Magento\Backend\App\Action\Context;

class Add extends \Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsUser
{
    public const ADMIN_RESOURCE = 'Olegnax_InstagramFeedPro::IntsUser_add';
    const URL_BASE_GRAPH = "https://graph.facebook.com/v21.0/";
    /**
     * @var AccountProcessor
     */
    protected $accountProcessor;

    /**
     * @param Context $context
     * @param AccountProcessor $accountProcessor
     */
    public function __construct(
        Context $context,
        AccountProcessor $accountProcessor
    ) {
        parent::__construct($context);
        $this->accountProcessor = $accountProcessor;
    }
    /**
     * Add Instagram Tokens Action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $token = $this->getRequest()->getParam('token');
        if ($token) {
            $data = [
                'access_token' => $token
            ];
            $response = $this->accountProcessor->getAccounts($data);
            if($response && is_array($response)){
                if (array_key_exists('error', $response)) {
                    $result->setData(['success' => false, 'message' => $response['error']]);
                } elseif(array_key_exists('success', $response)) {
                    $this->messageManager->addSuccessMessage(__('User was added successfully!'));
                    $result->setData(['success' => true]);
                }
            } else{
                $result->setData(['success' => false, 'message' => __('Empty response')]);
            }

        } else {
            $result->setData(['success' => false, 'message' => __('No data provided')]);
        }
        return $result;
    }
}