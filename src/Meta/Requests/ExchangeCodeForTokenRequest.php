<?php

namespace Dfikeda\SocialSdk\Meta\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Troca o `code` do login por um token de usuário de curta duração (GET /oauth/access_token).
 */
class ExchangeCodeForTokenRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $code,
        protected string $redirectUri,
        protected string $appId,
        protected string $appSecret,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/oauth/access_token';
    }

    protected function defaultQuery(): array
    {
        return [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $this->code,
        ];
    }
}
