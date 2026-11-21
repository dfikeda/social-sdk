<?php

namespace Dfikeda\SocialSdk\Support;

/**
 * Esconde credenciais que a Graph API leva na query string, para URL e mensagem de erro poderem ir para o log.
 */
class SensitiveDataRedactor
{
    /** @var list<string> */
    private const SENSITIVE_QUERY_KEYS = [
        'access_token',
        'appsecret_proof',
        'client_secret',
        'fb_exchange_token',
        'code',
    ];

    public static function redact(string $text): string
    {
        foreach (self::SENSITIVE_QUERY_KEYS as $key) {
            $text = preg_replace('/([?&]'.preg_quote($key, '/').'=)[^&\s"]+/i', '$1[REDACTED]', $text) ?? $text;
        }

        return $text;
    }
}
