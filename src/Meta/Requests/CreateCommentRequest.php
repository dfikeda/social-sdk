<?php

namespace Dfikeda\SocialSdk\Meta\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

/**
 * Comentário num post da Página ou numa mídia do Instagram: POST /{id}/comments.
 */
class CreateCommentRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $objectId,
        protected string $message,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->objectId).'/comments';
    }

    protected function defaultBody(): array
    {
        return ['message' => $this->message];
    }
}
