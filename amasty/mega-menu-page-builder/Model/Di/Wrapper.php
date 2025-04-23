<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Amasty Mega Menu PageBuilder for Magento 2 (System)
 */

namespace Amasty\MegaMenuPageBuilder\Model\Di;

class Wrapper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManagerInterface;
    /**
     * @var string
     */
    private $name;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        $name = ''
    ) {
        $this->objectManagerInterface = $objectManagerInterface;
        $this->name = $name;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        $result = false;
        if ($this->name && class_exists($this->name)) {
            $object = $this->objectManagerInterface->create($this->name);

            $result = call_user_func_array([$object, $name], $arguments);
        }

        return $result;
    }
}
