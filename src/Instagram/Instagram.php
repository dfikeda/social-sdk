<?php

namespace Dfikeda\SocialSdk\Instagram;

use Dfikeda\SocialSdk\Meta\MetaAuth;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Ponto de entrada do módulo Instagram (conta comercial ligada a uma Página; usa o token da Página).
 *
 *     Instagram::withToken($pageToken)->account($igUserId)->publish('Legenda', [$foto1, $foto2]);
 *     Instagram::withToken($pageToken)->account($igUserId)->publishingLimit();
 *     Instagram::withToken($pageToken)->media($mediaId)->comment('Link na bio');
 */
class Instagram
{
    /** O login é o mesmo da Meta (Facebook Login). */
    public static function auth(): MetaAuth
    {
        return new MetaAuth(new MetaGraphConnector);
    }

    public static function withToken(string $accessToken): InstagramClient
    {
        return new InstagramClient(new MetaGraphConnector($accessToken));
    }
}
