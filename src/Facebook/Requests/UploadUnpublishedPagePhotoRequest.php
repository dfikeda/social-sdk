<?php

namespace Dfikeda\SocialSdk\Facebook\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

/**
 * Sobe uma foto na Página sem publicar (POST /{page-id}/photos, published=false), para depois virar um post com
 * várias fotos. A Meta apaga a foto se ela não for usada em ~24 h.
 */
class UploadUnpublishedPagePhotoRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $pageId,
        protected string $imageUrl,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->pageId).'/photos';
    }

    protected function defaultBody(): array
    {
        return ['url' => $this->imageUrl, 'published' => 'false'];
    }
}
