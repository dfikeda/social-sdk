<?php

namespace Dfikeda\SocialSdk\Meta;

use RuntimeException;
use Saloon\Http\Response;

/**
 * Erro devolvido pela Graph API (`{"error": {"message", "type", "code", "error_subcode", "fbtrace_id"}}`).
 */
class MetaGraphException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $graphCode = 0,
        public readonly ?int $graphSubcode = null,
        public readonly int $httpStatus = 0,
    ) {
        parent::__construct($message);
    }

    public static function fromResponse(Response $response, string $action): self
    {
        $error = (array) ($response->json('error') ?? []);
        $message = (string) ($error['error_user_msg'] ?? $error['message'] ?? mb_substr(trim($response->body()), 0, 200));

        return new self(
            "{$action}: {$message}",
            (int) ($error['code'] ?? 0),
            isset($error['error_subcode']) ? (int) $error['error_subcode'] : null,
            $response->status(),
        );
    }

    /** Token vencido, revogado ou senha trocada (código 190): a conta precisa ser reconectada. */
    public function isTokenInvalid(): bool
    {
        return $this->graphCode === 190 || in_array($this->graphSubcode, [458, 459, 460, 463, 464, 467], true);
    }

    /** Limite de chamadas / de posts da Meta (4, 17, 32, 613; 9 = limite de posts do Instagram). */
    public function isRateLimited(): bool
    {
        return in_array($this->graphCode, [4, 9, 17, 32, 613], true);
    }
}
