<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Logger;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

class Handler extends BaseHandler
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = MonologLogger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/mirasvit/helpdesk.log';
}
