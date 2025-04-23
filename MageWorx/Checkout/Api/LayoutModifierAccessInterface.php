<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\Checkout\Api;

interface LayoutModifierAccessInterface
{
    const HANDLE_CUSTOM_MESSAGES = 'custom_message';

    /**
     * Return actual js layout (link)
     *
     * @return array
     */
    public function &getJsLayout(): array;

    /**
     * Input point to elements modifications
     *
     * @param string $code
     * @param array $element
     * @return array
     */
    public function preprocessElement(string $code, array &$element): array;
}
