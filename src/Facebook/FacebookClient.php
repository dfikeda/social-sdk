<?php

namespace Dfikeda\SocialSdk\Facebook;

use Dfikeda\SocialSdk\Facebook\Resources\FacebookPage;
use Dfikeda\SocialSdk\Facebook\Resources\FacebookPost;
use Dfikeda\SocialSdk\Facebook\Resources\FacebookUser;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Cliente do Facebook já com um token: escolha o recurso e encadeie a ação.
 */
class FacebookClient
{
    public function __construct(protected MetaGraphConnector $connector) {}

    /** Dono do token de usuário (para listar as Páginas que ele administra). */
    public function me(): FacebookUser
    {
        return new FacebookUser($this->connector);
    }

    public function page(string $pageId): FacebookPage
    {
        return new FacebookPage($this->connector, $pageId);
    }

    public function post(string $postId): FacebookPost
    {
        return new FacebookPost($this->connector, $postId);
    }

    public function connector(): MetaGraphConnector
    {
        return $this->connector;
    }
}
