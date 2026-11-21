<?php

namespace Dfikeda\SocialSdk\Instagram;

use Dfikeda\SocialSdk\Instagram\Resources\InstagramAccount;
use Dfikeda\SocialSdk\Instagram\Resources\InstagramMedia;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Cliente do Instagram já com um token: escolha o recurso e encadeie a ação.
 */
class InstagramClient
{
    public function __construct(protected MetaGraphConnector $connector) {}

    public function account(string $instagramUserId): InstagramAccount
    {
        return new InstagramAccount($this->connector, $instagramUserId);
    }

    public function media(string $mediaId): InstagramMedia
    {
        return new InstagramMedia($this->connector, $mediaId);
    }

    public function connector(): MetaGraphConnector
    {
        return $this->connector;
    }
}
