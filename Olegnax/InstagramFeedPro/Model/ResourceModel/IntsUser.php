<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class IntsUser extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('olegnax_instagramfeedpro_intsuser', 'intsuser_id');
    }
}
