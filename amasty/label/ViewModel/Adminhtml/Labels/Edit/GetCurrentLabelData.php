<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\ViewModel\Adminhtml\Labels\Edit;

use Magento\Framework\App\RequestInterface;

class GetCurrentLabelData
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    public function getLabelId(): ?int
    {
        $labelId = $this->request->getParam('id');

        return null === $labelId ? null : (int) $labelId;
    }

    public function isNewLabel(): bool
    {
        return $this->getLabelId() === null;
    }
}
