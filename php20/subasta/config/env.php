<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

(function () {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $envPath = dirname(__DIR__);
    if (is_readable($envPath . '/.env')) {
        Dotenv::createImmutable($envPath)->load();
    }
})();
