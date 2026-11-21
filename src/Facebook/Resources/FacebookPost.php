<?php

namespace Dfikeda\SocialSdk\Facebook\Resources;

use Dfikeda\SocialSdk\Meta\MetaGraphConnector;
use Dfikeda\SocialSdk\Meta\Requests\CreateCommentRequest;

/**
 * Post de uma Página.
 */
class FacebookPost
{
    public function __construct(
        protected MetaGraphConnector $connector,
        public readonly string $id,
    ) {}

    /** Comenta no post (ex.: o link no 1º comentário); devolve o id do comentário. */
    public function comment(string $message): string
    {
        return (string) $this->connector->sendOrFail(new CreateCommentRequest($this->id, $message), 'Comentar no Facebook')->json('id');
    }
}
