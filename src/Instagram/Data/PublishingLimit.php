<?php

namespace Dfikeda\SocialSdk\Instagram\Data;

/**
 * Posts publicados pela API nas últimas 24 h e o teto da conta.
 */
final readonly class PublishingLimit
{
    public function __construct(
        public int $used,
        public int $total,
    ) {}

    public function remaining(): int
    {
        return max(0, $this->total - $this->used);
    }
}
