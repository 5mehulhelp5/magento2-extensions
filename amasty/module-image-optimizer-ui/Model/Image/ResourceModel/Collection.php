<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Image Optimizer UI for Magento 2 (System)
 */

namespace Amasty\ImageOptimizerUi\Model\Image\ResourceModel;

use Amasty\ImageOptimizerUi\Model\Image\ImageSetting as Model;
use Amasty\ImageOptimizerUi\Model\Image\ResourceModel\ImageSetting as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_image_setting_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'amasty_image_setting';

    protected function _construct()
    {
        parent::_construct();
        $this->_init(Model::class, ResourceModel::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
