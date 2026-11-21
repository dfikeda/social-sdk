<?php

namespace Dfikeda\SocialSdk\Facebook\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Páginas que o usuário administra, cada uma com o token da Página e o Instagram comercial ligado (GET /me/accounts).
 */
class GetManagedPagesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/me/accounts';
    }

    protected function defaultQuery(): array
    {
        return [
            'fields' => 'id,name,access_token,picture{url},instagram_business_account{id,username,name,profile_picture_url}',
            'limit' => 100,
        ];
    }
}
