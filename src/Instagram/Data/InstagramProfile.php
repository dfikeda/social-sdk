<?php

namespace Dfikeda\SocialSdk\Instagram\Data;

/**
 * Conta comercial do Instagram ligada a uma Página do Facebook.
 */
final readonly class InstagramProfile
{
    public function __construct(
        public string $id,
        public ?string $username,
        public ?string $name,
        public ?string $pictureUrl,
    ) {}

    /** @param  array<string, mixed>  $data */
    public static function fromGraph(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            username: $data['username'] ?? null,
            name: $data['name'] ?? null,
            pictureUrl: $data['profile_picture_url'] ?? null,
        );
    }
}
