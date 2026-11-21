<?php

namespace Dfikeda\SocialSdk\Instagram\Resources;

use Dfikeda\SocialSdk\Meta\MetaGraphConnector;
use Dfikeda\SocialSdk\Meta\Requests\CreateCommentRequest;

/**
 * Mídia publicada no Instagram.
 */
class InstagramMedia
{
    public function __construct(
        protected MetaGraphConnector $connector,
        public readonly string $id,
    ) {}

    /** Comenta na mídia; devolve o id do comentário. Link em comentário do Instagram não é clicável. */
    public function comment(string $message): string
    {
        return (string) $this->connector->sendOrFail(new CreateCommentRequest($this->id, $message), 'Comentar no Instagram')->json('id');
    }
}
