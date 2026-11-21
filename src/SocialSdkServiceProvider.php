<?php

namespace Dfikeda\SocialSdk;

use Illuminate\Support\ServiceProvider;

/**
 * SDK das redes sociais (módulos Facebook e Instagram, base comum em Meta). Só cliente HTTP: não guarda token.
 */
class SocialSdkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/social.php', 'social');
    }
}
