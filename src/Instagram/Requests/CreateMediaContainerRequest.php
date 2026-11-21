<?php

namespace Dfikeda\SocialSdk\Instagram\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

/**
 * Container de mídia do Instagram (POST /{ig-user-id}/media), em três formas:
 * - foto do carrossel: image_url + is_carousel_item=true;
 * - carrossel: media_type=CAROUSEL + children (ids separados por vírgula, até 10) + caption;
 * - foto única: image_url + caption.
 * A foto precisa ser JPEG numa URL pública. O container expira em 24 h se não for publicado.
 */
class CreateMediaContainerRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    /** @param  array<string, string>  $fields */
    public function __construct(
        protected string $instagramUserId,
        protected array $fields,
    ) {}

    public static function carouselItem(string $instagramUserId, string $imageUrl): self
    {
        return new self($instagramUserId, ['image_url' => $imageUrl, 'is_carousel_item' => 'true']);
    }

    /** @param  list<string>  $childrenIds */
    public static function carousel(string $instagramUserId, array $childrenIds, string $caption): self
    {
        return new self($instagramUserId, ['media_type' => 'CAROUSEL', 'children' => implode(',', $childrenIds), 'caption' => $caption]);
    }

    public static function singleImage(string $instagramUserId, string $imageUrl, string $caption): self
    {
        return new self($instagramUserId, ['image_url' => $imageUrl, 'caption' => $caption]);
    }

    /** @return array<string, string> */
    public function fields(): array
    {
        return $this->fields;
    }

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->instagramUserId).'/media';
    }

    protected function defaultBody(): array
    {
        return $this->fields;
    }
}
