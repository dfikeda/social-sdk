<?php

namespace Dfikeda\SocialSdk\Meta\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Lê campos de qualquer objeto da Graph (post, mídia, container): GET /{id}?fields=...
 */
class GetGraphObjectRequest extends Request
{
    protected Method $method = Method::GET;

    /** @param  list<string>  $fields */
    public function __construct(
        protected string $objectId,
        protected array $fields,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->objectId);
    }

    protected function defaultQuery(): array
    {
        return ['fields' => implode(',', $this->fields)];
    }
}
