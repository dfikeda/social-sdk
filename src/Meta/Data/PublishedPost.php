<?php

namespace Dfikeda\SocialSdk\Meta\Data;

/**
 * Post publicado (Facebook ou Instagram). `permalink` pode faltar se a Graph não devolver o link na hora.
 */
final readonly class PublishedPost
{
    public function __construct(
        public string $id,
        public ?string $permalink,
    ) {}
}
