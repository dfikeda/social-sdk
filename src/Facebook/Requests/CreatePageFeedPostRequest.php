<?php

namespace Dfikeda\SocialSdk\Facebook\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

/**
 * Post na Página com texto e as fotos já enviadas (POST /{page-id}/feed, attached_media[n]={"media_fbid": id}).
 */
class CreatePageFeedPostRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    /** @param  list<string>  $photoIds */
    public function __construct(
        protected string $pageId,
        protected string $message,
        protected array $photoIds,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->pageId).'/feed';
    }

    protected function defaultBody(): array
    {
        $body = ['message' => $this->message];

        if ($this->photoIds !== []) {
            $body['attached_media'] = array_map(
                fn (string $id): string => (string) json_encode(['media_fbid' => $id]),
                array_values($this->photoIds),
            );
        }

        return $body;
    }
}
