# dfikeda/social-sdk

SDK Laravel (sobre [Saloon](https://docs.saloon.dev) v4) para publicar em redes sociais. Hoje: **Facebook**
(Páginas) e **Instagram** (conta comercial ligada a uma Página), pela Graph API da Meta.

O pacote é só cliente HTTP: não guarda token. Guardar os tokens (cifrados) é com o app.

## Configuração

```env
META_APP_ID=
META_APP_SECRET=
META_GRAPH_VERSION=v26.0
```

Com o segredo do app configurado, toda chamada vai com `appsecret_proof`.

## Uso

```php
use Dfikeda\SocialSdk\Facebook\Facebook;
use Dfikeda\SocialSdk\Instagram\Instagram;

// Login (o mesmo para Facebook e Instagram)
$url = Facebook::auth()->authorizationUrl($redirectUri, $state);
$userToken = Facebook::auth()->exchangeCodeForLongLivedToken($code, $redirectUri);

// Páginas do usuário, cada uma com o token da Página e o Instagram ligado
$pages = Facebook::withToken($userToken)->me()->pages();

// Facebook
$post = Facebook::withToken($pageToken)->page($pageId)->publish('Texto', [$fotoUrl1, $fotoUrl2]);
Facebook::withToken($pageToken)->post($post->id)->comment('Link: https://...');

// Instagram (token da Página; 1 foto = post simples, 2 a 10 = carrossel; fotos em JPEG com URL pública)
$media = Instagram::withToken($pageToken)->account($igUserId)->publish('Legenda', [$fotoUrl1, $fotoUrl2]);
Instagram::withToken($pageToken)->media($media->id)->comment('Link na bio');
$limit = Instagram::withToken($pageToken)->account($igUserId)->publishingLimit(); // ->used, ->total, ->remaining()
```

Erro da Graph vira `Dfikeda\SocialSdk\Meta\MetaGraphException`. `isTokenInvalid()` diz quando é preciso conectar
de novo e `isRateLimited()` quando a Meta limitou as chamadas.

## Permissões pedidas no login

`pages_show_list`, `pages_read_engagement`, `pages_manage_posts`, `business_management`, `instagram_basic`,
`instagram_content_publish`, `instagram_manage_comments` (ajustáveis em `config('social.meta.scopes')`).
