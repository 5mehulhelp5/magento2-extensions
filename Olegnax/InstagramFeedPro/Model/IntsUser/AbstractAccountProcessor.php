<?php
namespace Olegnax\InstagramFeedPro\Model\IntsUser;

use Exception;
use Olegnax\InstagramFeedPro\Model\Data\IntsUser as DataIntsUser;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsPost\CollectionFactory as InstPostsCollectionFactory;
use Olegnax\InstagramFeedPro\Model\IntsUser;
use Magento\Framework\Encryption\EncryptorInterface;
use Olegnax\InstagramFeedPro\Model\DownloadMedia;
use Psr\Log\LoggerInterface;

class AbstractAccountProcessor
{
    protected $client;

    /**
     * @var IntsUser
     */
    protected $model;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DownloadMedia
     */
    protected $downloadMedia;
    /**
     * @var InstPostsCollectionFactory
     */
    protected $postsCollectionFactory;

    public function __construct(
        IntsUser $model,
        EncryptorInterface $encryptor,
        DownloadMedia $downloadMedia,
        InstPostsCollectionFactory $postsCollectionFactory,
        LoggerInterface $logger,
        $client
    ) {
        $this->model = $model;
        $this->encryptor = $encryptor;
        $this->downloadMedia = $downloadMedia;
        $this->postsCollectionFactory = $postsCollectionFactory;
        $this->logger = $logger;
        $this->client = $client;
    }
    public function getAccounts(array $result){
        return [];
    }
    /**
     * Create new or update existing user
     *
     * @param array $accounts
     * @param array $result
     * @return void
     */
    public function processAccounts(array $accounts, array $result)
    {
        $existUsersbyId = [];
        $existUsersbyUsername = [];
        $usersCollection = $this->model->getCollection()->load();

        foreach ($usersCollection as $item) {
            // Store the user object directly
            $existUsersbyId[(int)$item->getUserId()] = $item;
            $existUsersbyUsername[$item->getUsername()] = $item;
        }

        foreach ($accounts as $account) {
            try {
                $result = $this->prepareUserData($account, $result);

                if (array_key_exists($result[DataIntsUser::USER_ID], $existUsersbyId)) { // User exists by ID
                    $this->updateExistingUser($existUsersbyId[$result[DataIntsUser::USER_ID]], $result);
                } elseif (array_key_exists($result[DataIntsUser::USERNAME], $existUsersbyUsername)) { // User exists by Username
                    // save old user id for posts before it is updated
                    $oldUser = $existUsersbyUsername[$result[DataIntsUser::USERNAME]]->getUserId();
                    $this->updateExistingUser($existUsersbyUsername[$result[DataIntsUser::USERNAME]], $result);
                    /*
                    * post owners are stored as account ids instead of usernames which differs depending on account type / token.
                    * We need to update owner for existing posts with the new user id.
                    */
                    $this->updatePostOwners($oldUser, $result[DataIntsUser::USER_ID]);
                } else {
                    // New user, save it
                    $result['access_token'] = $this->encryptor->encrypt($result['access_token']);
                    $this->model->addData($result)->save();
                }
    
            } catch (Exception $e) {
                $this->error->error("Instagram Error: " . $e->getMessage());
            }
        }
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
        $result[DataIntsUser::ACCOUNT_TYPE] = $userData['account_type'];
        $result[DataIntsUser::MEDIA_COUNT] = $userData['media_count'];
        $result[DataIntsUser::PROFILE_PICTURE] = $this->downloadMedia->downloadMedia($userData['profile_picture_url'], 'profile_picture_');       
        $result[DataIntsUser::EXPIRE] = $result['expire'];
        
        return $result;
    }
    /**
     * Update existing user.
     * Used to update existing users by their object.
     *
     * @param IntsUser $user
     * @param array $result
     * @return void
     */
    public function updateExistingUser($user, $result)
    {
        $user->setData(DataIntsUser::USER_ID, $result[DataIntsUser::USER_ID]);
        $user->setData(DataIntsUser::USERNAME, $result[DataIntsUser::USERNAME]);
        $user->setData(DataIntsUser::ACCOUNT_TYPE, $result[DataIntsUser::ACCOUNT_TYPE]);
        $user->setData(DataIntsUser::ACCESS_TOKEN, $this->encryptor->encrypt($result['access_token']));
        $user->setData(DataIntsUser::PROFILE_PICTURE, $result[DataIntsUser::PROFILE_PICTURE]);
        $user->setData(DataIntsUser::EXPIRE, $result[DataIntsUser::EXPIRE]);
        $user->setData(DataIntsUser::MEDIA_COUNT, $result[DataIntsUser::MEDIA_COUNT]);
        $user->save();
    }
    /**
     * Update existing posts owner id
     *
     * @param string $oldOwner
     * @param string $newOwner
     * @return void
     */
    public function updatePostOwners($oldOwner, $newOwner)
    {
        // Load the posts collection
        $postsCollection = $this->postsCollectionFactory->create();
        $postsCollection->load();

        foreach ($postsCollection as $post) {
            try {
                $currentOwner = $post->getOwner();
                if ($currentOwner === $oldOwner) {
                    $post->setOwner($newOwner);
                    $post->save();
                }
            } catch (Exception $e) {
                $this->logger->error("Instagram: Error updating post owner for post ID " . $post->getId() . ": " . $e->getMessage());
            }
        }
    }
}
