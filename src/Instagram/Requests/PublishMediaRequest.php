<?php

namespace Dfikeda\SocialSdk\Instagram\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

/**
 * Publica um container pronto (POST /{ig-user-id}/media_publish). É aqui que conta o limite de posts por 24 h.
 */
class PublishMediaRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $instagramUserId,
        protected string $containerId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->instagramUserId).'/media_publish';
    }

    protected function defaultBody(): array
    {
        return ['creation_id' => $this->containerId];
    }
}
