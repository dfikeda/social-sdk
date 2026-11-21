<?php

namespace Dfikeda\SocialSdk\Instagram\Resources;

use Dfikeda\SocialSdk\Instagram\Data\PublishingLimit;
use Dfikeda\SocialSdk\Instagram\Requests\CreateMediaContainerRequest;
use Dfikeda\SocialSdk\Instagram\Requests\GetContentPublishingLimitRequest;
use Dfikeda\SocialSdk\Instagram\Requests\PublishMediaRequest;
use Dfikeda\SocialSdk\Meta\Data\PublishedPost;
use Dfikeda\SocialSdk\Meta\MetaGraphConnector;
use Dfikeda\SocialSdk\Meta\MetaGraphException;
use InvalidArgumentException;

/**
 * Conta comercial do Instagram.
 */
class InstagramAccount
{
    /** Fotos por carrossel. */
    public const MAX_CAROUSEL_IMAGES = 10;

    public function __construct(
        protected MetaGraphConnector $connector,
        public readonly string $id,
    ) {}

    /**
     * Publica: 1 foto = container simples; 2 a 10 = carrossel. Espera cada container ficar pronto antes de publicar.
     * As fotos precisam ser JPEG em URL pública.
     *
     * @param  list<string>  $imageUrls
     */
    public function publish(string $caption, array $imageUrls): PublishedPost
    {
        $imageUrls = array_slice(array_values($imageUrls), 0, self::MAX_CAROUSEL_IMAGES);

        if ($imageUrls === []) {
            throw new InvalidArgumentException('O Instagram não aceita post sem foto.');
        }

        if (count($imageUrls) === 1) {
            $containerId = $this->createContainer(CreateMediaContainerRequest::singleImage($this->id, $imageUrls[0], $caption));
        } else {
            $children = array_map(
                fn (string $url): string => $this->createContainer(CreateMediaContainerRequest::carouselItem($this->id, $url)),
                $imageUrls,
            );
            $containerId = $this->createContainer(CreateMediaContainerRequest::carousel($this->id, $children, $caption));
        }

        $mediaId = (string) $this->connector
            ->sendOrFail(new PublishMediaRequest($this->id, $containerId), 'Publicar no Instagram')
            ->json('id');

        return new PublishedPost($mediaId, $this->connector->field($mediaId, 'permalink'));
    }

    public function publishingLimit(): PublishingLimit
    {
        $row = (array) ($this->connector
            ->sendOrFail(new GetContentPublishingLimitRequest($this->id), 'Ler o limite de posts')
            ->json('data.0') ?? []);

        return new PublishingLimit((int) ($row['quota_usage'] ?? 0), (int) ($row['config']['quota_total'] ?? 50));
    }

    private function createContainer(CreateMediaContainerRequest $request): string
    {
        $containerId = (string) $this->connector->sendOrFail($request, 'Preparar foto no Instagram')->json('id');
        $this->waitForContainer($containerId);

        return $containerId;
    }

    /** O container fica IN_PROGRESS por alguns segundos; ERROR/EXPIRED não adianta esperar. */
    private function waitForContainer(string $containerId): void
    {
        $checks = max(1, (int) config('social.meta.container_status_checks', 5));
        $waitMs = max(0, (int) config('social.meta.container_status_wait_ms', 2000));

        for ($check = 1; $check <= $checks; $check++) {
            $status = $this->connector->field($containerId, 'status_code');

            if ($status === null || $status === 'FINISHED' || $status === 'PUBLISHED') {
                return;
            }

            if (in_array($status, ['ERROR', 'EXPIRED'], true)) {
                throw new MetaGraphException("O Instagram recusou a foto (status {$status}). Ela precisa ser JPEG numa URL pública.");
            }

            if ($check < $checks && $waitMs > 0) {
                usleep($waitMs * 1000);
            }
        }

        throw new MetaGraphException('O Instagram demorou demais para preparar as fotos. Tente de novo em alguns minutos.');
    }
}
