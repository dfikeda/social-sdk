<?php

namespace Dfikeda\SocialSdk\Facebook\Resources;

use Dfikeda\SocialSdk\Facebook\Data\ManagedPage;
use Dfikeda\SocialSdk\Facebook\Requests\GetManagedPagesRequest;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Usuário dono do token (`/me`).
 */
class FacebookUser
{
    public function __construct(protected MetaGraphConnector $connector) {}

    /**
     * Páginas que o usuário administra e liberou no login (as sem token ficam de fora).
     *
     * @return list<ManagedPage>
     */
    public function pages(): array
    {
        $response = $this->connector->sendOrFail(new GetManagedPagesRequest, 'Listar as Páginas');

        return collect((array) $response->json('data', []))
            ->map(fn (array $page): ManagedPage => ManagedPage::fromGraph($page))
            ->filter(fn (ManagedPage $page): bool => $page->accessToken !== '')
            ->values()
            ->all();
    }
}
