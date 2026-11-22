<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta (Página do Facebook + Instagram comercial)
    |--------------------------------------------------------------------------
    | App criado em developers.facebook.com. O pacote não guarda token: o token de cada Página/Instagram fica com o
    | app que usa o SDK. Sem app_id/app_secret, MetaAuth::isConfigured() devolve false.
    */
    'meta' => [
        'app_id' => env('META_APP_ID'),
        'app_secret' => env('META_APP_SECRET'),
        'graph_version' => env('META_GRAPH_VERSION', 'v26.0'),

        // URL de volta do login (a mesma cadastrada no app da Meta). Vazio = o app decide.
        'redirect_uri' => env('META_REDIRECT_URI'),

        // Permissões pedidas no login do Facebook.
        'scopes' => [
            'pages_show_list',
            'pages_read_engagement',
            'pages_manage_posts',
            'pages_manage_engagement',
            'business_management',
            'instagram_basic',
            'instagram_content_publish',
            'instagram_manage_comments',
        ],

        // Container do Instagram fica pronto em segundos; quantas vezes conferir o status antes de desistir.
        'container_status_checks' => (int) env('META_CONTAINER_STATUS_CHECKS', 5),
        'container_status_wait_ms' => (int) env('META_CONTAINER_STATUS_WAIT_MS', 2000),
    ],

    // Repetição de leituras (GET) com falha passageira; escrita nunca é repetida.
    'retry' => [
        'tries' => 3,
        'interval_ms' => 1000,
    ],
];
