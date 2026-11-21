<?php

namespace Dfikeda\SocialSdk\Facebook\Data;

use Dfikeda\SocialSdk\Instagram\Data\InstagramProfile;

/**
 * Página que o usuário administra, com o token da Página e o Instagram comercial ligado (se houver).
 * O token é segredo: guarde cifrado e nunca mande para o navegador.
 */
final readonly class ManagedPage
{
    public function __construct(
        public string $id,
        public string $name,
        public string $accessToken,
        public ?string $pictureUrl,
        public ?InstagramProfile $instagram,
    ) {}

    /** @param  array<string, mixed>  $data  item de GET /me/accounts */
    public static function fromGraph(array $data): self
    {
        $instagram = $data['instagram_business_account'] ?? null;

        return new self(
            id: (string) $data['id'],
            name: (string) ($data['name'] ?? ''),
            accessToken: (string) ($data['access_token'] ?? ''),
            pictureUrl: $data['picture']['data']['url'] ?? null,
            instagram: is_array($instagram) ? InstagramProfile::fromGraph($instagram) : null,
        );
    }
}
