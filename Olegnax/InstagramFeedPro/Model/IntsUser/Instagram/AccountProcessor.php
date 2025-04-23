<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
namespace Olegnax\InstagramFeedPro\Model\IntsUser\Instagram;

use Exception;
use Olegnax\InstagramFeedPro\Model\Instagram\Client;
use Olegnax\InstagramFeedPro\Model\IntsUser\AbstractAccountProcessor;
use Olegnax\InstagramFeedPro\Model\Data\IntsUser as DataIntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory as InstPostsCollectionFactory;
use Olegnax\InstagramFeedPro\Model\IntsUser;
use Magento\Framework\Encryption\EncryptorInterface;
use Olegnax\InstagramFeedPro\Model\DownloadMedia;
use Psr\Log\LoggerInterface;

class AccountProcessor extends AbstractAccountProcessor
{
    public function __construct(
        IntsUser $model,
        EncryptorInterface $encryptor,
        DownloadMedia $downloadMedia,
        InstPostsCollectionFactory $postsCollectionFactory,
        LoggerInterface $logger,
        Client $client
    ) {
        parent::__construct($model, $encryptor, $downloadMedia, $postsCollectionFactory, $logger, $client);
    }

    public function getAccounts(array $data){
        $response = '';

        if (!isset($data['user_id'])) {
            $response = ['error' => __('No available accounts were found!')];
        } elseif (!isset($data['access_token'])) {
            $response = ['error' => __('Access token not found!')];
        } else {
            $data['id'] = $data['user_id'];
            try{
                $this->processAccounts([$data], $data);
                $response = ['success' => true];
            } catch (Exception $e) {
                $response = ['error' => $e->getMessage()];
            }
        }

        return $response;
    }    

}