<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Model\Frontend\Rating\VotingRequest;

interface VotingRequestInterface
{
    public function getQuestionId(): int;

    public function getValue(): string;

    public function isRevote(): bool;

    public function getOldValue(): ?string;
}
