<?php

namespace Dfikeda\SocialSdk\Meta;

use Dfikeda\SocialSdk\Meta\Requests\GetGraphObjectRequest;
use Dfikeda\SocialSdk\Support\SensitiveDataRedactor;
use Illuminate\Support\Facades\Log;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;

/**
 * Conector Saloon da Graph API da Meta (Página do Facebook + Instagram comercial).
 *
 * O token vai na query (`access_token`, forma canônica da Graph; o SensitiveDataRedactor esconde nos logs) e, com
 * o segredo do app configurado, também o `appsecret_proof` (HMAC do token), que impede usar um token vazado fora
 * do nosso servidor. Versão da API em `social.meta.graph_version`.
 * Leitura (GET) com falha passageira é repetida; escrita nunca (não dá para saber se a Meta publicou).
 */
class MetaGraphConnector extends Connector
{
    use AcceptsJson;

    /** @var list<int> */
    private const TRANSIENT_HTTP_STATUSES = [502, 503, 504];

    public function __construct(
        protected ?string $accessToken = null,
        protected ?string $appSecret = null,
    ) {
        $this->appSecret ??= config('social.meta.app_secret') ?: null;

        $this->tries = max(1, (int) config('social.retry.tries', 3));
        $this->retryInterval = max(0, (int) config('social.retry.interval_ms', 1000));
        $this->useExponentialBackoff = true;
        $this->throwOnMaxTries = false;

        $this->middleware()->onResponse(function (Response $response) {
            if ($response->status() < 500) {
                return;
            }

            Log::error('Meta Graph API Error', [
                'status' => $response->status(),
                'method' => $response->getPendingRequest()->getMethod()->value,
                'url' => SensitiveDataRedactor::redact((string) $response->getPsrRequest()->getUri()),
                'message' => mb_substr(trim($response->body()), 0, 300),
            ]);
        });
    }

    public function resolveBaseUrl(): string
    {
        return 'https://graph.facebook.com/'.trim((string) config('social.meta.graph_version', 'v26.0'), '/');
    }

    protected function defaultQuery(): array
    {
        if ($this->accessToken === null || $this->appSecret === null) {
            return [];
        }

        return ['appsecret_proof' => hash_hmac('sha256', $this->accessToken, $this->appSecret)];
    }

    protected function defaultAuth(): ?Authenticator
    {
        return $this->accessToken ? new QueryAuthenticator('access_token', $this->accessToken) : null;
    }

    /** Envia e transforma qualquer erro da Graph em {@see MetaGraphException}. */
    public function sendOrFail(Request $request, string $action): Response
    {
        $response = $this->send($request);

        if ($response->failed()) {
            throw MetaGraphException::fromResponse($response, $action);
        }

        return $response;
    }

    /** Um campo de um objeto; null se a Graph não devolver (não derruba o que já foi feito). */
    public function field(string $objectId, string $field): ?string
    {
        $response = $this->send(new GetGraphObjectRequest($objectId, [$field]));
        $value = $response->successful() ? $response->json($field) : null;

        return $value === null ? null : (string) $value;
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if ($request->getMethod() !== Method::GET) {
            return false;
        }

        return $exception instanceof FatalRequestException
            || in_array($exception->getResponse()->status(), self::TRANSIENT_HTTP_STATUSES, true);
    }
}
