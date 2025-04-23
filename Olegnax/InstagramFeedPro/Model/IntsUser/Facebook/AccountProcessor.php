<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2024 Olegnax (http://olegnax.com/). All rights reserved.
 */
namespace Olegnax\InstagramFeedPro\Model\IntsUser\Facebook;

use Exception;
use Olegnax\InstagramFeedPro\Model\Facebook\Client;
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

        if (isset($data['access_token'])) {
            $this->client->setToken($data['access_token']);

            try{
                $accounts = $this->client->getInstagramBusinessAccounts();
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }

            if (empty($accounts)) {
                $response = ['error' => __('No available accounts were found!')];
            } else {
                try{
                    $this->processAccounts($accounts, $data);
                    $response = ['success' => true];
                } catch (Exception $e) {
                    $response = ['error' => $e->getMessage()];
                }
            }
        } else {
            $response = ['error' => __('Access token not found!')];
        }
        return $response;
    }

    /**
     * Prepare user data before save
     *
     * @param array $account
     * @param array $result
     * @return array
     */
    public function prepareUserData(array $account, array $result){
        $userData = $this->client
        ->setUserId($account['id'])
        ->setToken($account['access_token'])
        ->getUsername();

        $result[DataIntsUser::USER_ID] = $userData['id'];
        $result[DataIntsUser::USERNAME] = $userData['username'];
        $result[DataIntsUser::ACCOUNT_TYPE] = DataIntsUser::ACCOUNT_TYPE_BUSINESS;
        $result[DataIntsUser::MEDIA_COUNT] = $userData['media_count'];
        $result[DataIntsUser::PROFILE_PICTURE] = $this->downloadMedia->downloadMedia($userData['profile_picture_url'], 'profile_picture_');
        // try to get token expiration using debug in case it is a manually added facebook token which wasn't extended
        // *extended tokens do not have expiraton time
        $result[DataIntsUser::EXPIRE] = $result['expire'] ?? $this->client->getTockenExpiration();
        
        return $result;
    }
}
