<?php

namespace Dfikeda\SocialSdk\Meta;

use Dfikeda\SocialSdk\Meta\Requests\ExchangeCodeForTokenRequest;
use Dfikeda\SocialSdk\Meta\Requests\ExchangeLongLivedTokenRequest;

/**
 * Login da Meta (Facebook Login), comum ao Facebook e ao Instagram comercial: link de autorização e troca do
 * `code` por um token de usuário longo (~60 dias). Com o token longo, `Facebook::withToken($t)->me()->pages()`
 * devolve as Páginas com o token de cada uma (token de Página tirado de token longo não expira).
 */
class MetaAuth
{
    public function __construct(protected MetaGraphConnector $connector) {}

    public function isConfigured(): bool
    {
        return filled(config('social.meta.app_id')) && filled(config('social.meta.app_secret'));
    }

    /** Link do login que pede as permissões de `social.meta.scopes`. */
    public function authorizationUrl(string $redirectUri, string $state): string
    {
        $version = trim((string) config('social.meta.graph_version', 'v26.0'), '/');

        return "https://www.facebook.com/{$version}/dialog/oauth?".http_build_query([
            'client_id' => (string) config('social.meta.app_id'),
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'response_type' => 'code',
            'scope' => implode(',', (array) config('social.meta.scopes', [])),
        ]);
    }

    /** `code` do callback → token de usuário longo. */
    public function exchangeCodeForLongLivedToken(string $code, string $redirectUri): string
    {
        $appId = (string) config('social.meta.app_id');
        $appSecret = (string) config('social.meta.app_secret');

        $short = $this->connector->sendOrFail(new ExchangeCodeForTokenRequest($code, $redirectUri, $appId, $appSecret), 'Trocar o código do login');
        $long = $this->connector->sendOrFail(new ExchangeLongLivedTokenRequest((string) $short->json('access_token'), $appId, $appSecret), 'Gerar token longo');

        return (string) $long->json('access_token');
    }
}
