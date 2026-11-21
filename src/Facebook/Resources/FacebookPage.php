<?php

namespace Dfikeda\SocialSdk\Facebook\Resources;

use Dfikeda\SocialSdk\Facebook\Requests\CreatePageFeedPostRequest;
use Dfikeda\SocialSdk\Facebook\Requests\UploadUnpublishedPagePhotoRequest;
use Dfikeda\SocialSdk\Meta\Data\PublishedPost;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;

/**
 * Página do Facebook (precisa do token da Página).
 */
class FacebookPage
{
    /** Fotos por post (o mesmo teto do carrossel do Instagram). */
    public const MAX_PHOTOS = 10;

    public function __construct(
        protected MetaGraphConnector $connector,
        public readonly string $id,
    ) {}

    /**
     * Publica um post com texto e fotos: cada foto sobe sem publicar e entra em `attached_media`.
     *
     * @param  list<string>  $imageUrls  URLs públicas
     */
    public function publish(string $message, array $imageUrls = []): PublishedPost
    {
        $photoIds = [];

        foreach (array_slice(array_values($imageUrls), 0, self::MAX_PHOTOS) as $url) {
            $photoIds[] = (string) $this->connector
                ->sendOrFail(new UploadUnpublishedPagePhotoRequest($this->id, $url), 'Enviar foto ao Facebook')
                ->json('id');
        }

        $postId = (string) $this->connector
            ->sendOrFail(new CreatePageFeedPostRequest($this->id, $message, $photoIds), 'Publicar no Facebook')
            ->json('id');

        return new PublishedPost($postId, $this->connector->field($postId, 'permalink_url'));
    }
}
