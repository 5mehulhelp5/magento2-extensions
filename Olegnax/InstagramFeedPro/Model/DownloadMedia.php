<?php
namespace Olegnax\InstagramFeedPro\Model;

use Exception;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Olegnax\InstagramFeedPro\Controller\Adminhtml\IntsPost\Update;

class DownloadMedia
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * @var File
     */
    protected $file;
   
    /**
     * Constructor
     *
     * @param DirectoryList $directoryList
     * @param File $file
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file
    ) {
        $this->directoryList = $directoryList;
        $this->file = $file;
    }
    /**
     * @param string $imageUrl
     * @param string $prefix
     * @return string
     * @throws FileSystemException
     */
    public function downloadMedia($imageUrl, $prefix = '')
    {
        if (empty($imageUrl)) {
            return $imageUrl;
        }
        $fileName = $prefix . baseName(parse_url($imageUrl, PHP_URL_PATH));
        $tempFileName = $this->getMediaDirTmpDir() . $fileName;
        $newFileName = $this->getMediaDirDestDir() . $fileName;

        if ($this->file->fileExists($newFileName, true)) {
            return $this->prepareUploadFile($newFileName);
        }

        try {
            $this->file->checkAndCreateFolder(dirname($tempFileName));
            ob_start();
            $result = $this->file->write($tempFileName, $this->file->read($imageUrl), 0755);
            $echo = ob_get_clean();
            if (!$result) {
                throw new Exception($echo);
            }
            $this->file->checkAndCreateFolder(dirname($newFileName));
            $result = $this->file->mv($tempFileName, $newFileName);
            if (!$result) {
                throw new Exception(__("it is not possible to move a file from '{$tempFileName}' to '{$newFileName}'")->getText());
            }
            return $this->prepareUploadFile($newFileName);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $imageUrl;
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirTmpDir()
    {
        return $this->getMediaDir() . '/tmp/';
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA);
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirDestDir()
    {
        return $this->getMediaDir() . '/' . Update::UPLOAD_DIR . '/';
    }

    /**
     * @param string $path
     * @return string
     * @throws FileSystemException
     */
    protected function prepareUploadFile($path)
    {
        return str_replace($this->getMediaDir() . '/', '', $path);
    }
}