<?php

namespace Dfikeda\SocialSdk\Instagram\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Quantos posts a conta publicou pela API nas últimas 24 h e o teto (GET /{ig-user-id}/content_publishing_limit).
 */
class GetContentPublishingLimitRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $instagramUserId) {}

    public function resolveEndpoint(): string
    {
        return '/'.rawurlencode($this->instagramUserId).'/content_publishing_limit';
    }

    protected function defaultQuery(): array
    {
        return ['fields' => 'quota_usage,config'];
    }
}
