<?php

namespace Dfikeda\SocialSdk\Facebook;

use Dfikeda\SocialSdk\Meta\MetaAuth;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Ponto de entrada do módulo Facebook (Páginas).
 *
 *     Facebook::auth()->authorizationUrl($redirectUri, $state);
 *     Facebook::withToken($userToken)->me()->pages();
 *     Facebook::withToken($pageToken)->page($pageId)->publish('Texto', [$fotoUrl]);
 *     Facebook::withToken($pageToken)->post($postId)->comment('Link: https://...');
 */
class Facebook
{
    public static function auth(): MetaAuth
    {
        return new MetaAuth(new MetaGraphConnector);
    }

    public static function withToken(string $accessToken): FacebookClient
    {
        return new FacebookClient(new MetaGraphConnector($accessToken));
    }
}
