<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Olegnax\InstagramFeedPro\Model\ResourceModel\IntsUser;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'intsuser_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Olegnax\InstagramFeedPro\Model\IntsUser::class,
            IntsUser::class
        );
    }
}
