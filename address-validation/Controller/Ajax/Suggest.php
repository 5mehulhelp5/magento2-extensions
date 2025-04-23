<?php
/**
 * @author Azaleasoft Team
 * @copyright Copyright (c) 2018 Azaleasoft (https://www.azaleasoft.com)
 * @package Azaleasoft_Asaddressvalidation
 */
namespace Azaleasoft\Asaddressvalidation\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;

class Suggest extends \Magento\Framework\App\Action\Action
{
    private $helper;

    public function __construct(
        \Azaleasoft\Asaddressvalidation\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $responseData = [];

        try {
            if ($this->getRequest()->isAjax() && $this->getRequest()->isPost()) {
                $postData = $this->getRequest()->getPost();
                $regionData = $this->helper->getRegionData($postData);
                $postData['region_code'] = isset($regionData['region_code']) ? $regionData['region_code'] : '';
                $postData['region'] = isset($regionData['region']) ? $regionData['region'] : $postData['region'];
                $postData['fulladdress'] = $this->helper->getFullAddress($postData);
                $postData['type'] = 'O';
                $responseData[] = $postData;

                $rs = $this->helper->sendRequest($postData);
                if (!empty($rs)) {
                    foreach ($rs as $row) {
                        $regionData = $this->helper->getRegionData($row);
                        $row['region_id'] = isset($regionData['region_id']) ? $regionData['region_id'] : '';
                        $row['region_code'] = isset($regionData['region_code']) ? $regionData['region_code'] : '';
                        $row['region'] = isset($regionData['region']) ? $regionData['region'] : '';
                        $row['fulladdress'] = $this->helper->getFullAddress($row);
                        $row['type'] = 'S';
                        $responseData[] = $row;
                    }
                }
            }
        } catch (\Exception $e) {

        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
