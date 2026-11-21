<?php

namespace Dfikeda\SocialSdk\Meta\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Token de usuário curto → longo (~60 dias), GET /oauth/access_token com grant_type=fb_exchange_token.
 */
class ExchangeLongLivedTokenRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $shortLivedToken,
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
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $this->shortLivedToken,
        ];
    }
}
