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

class FatalLogger extends \Monolog\Logger
{
    /**
     * FatalLogger constructor.
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        $name,
        $handlers = [],
        $processors = []
    ) {

        parent::__construct($name, $handlers, $processors);
    }
}
