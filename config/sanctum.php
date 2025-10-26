<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Dominios con Estado
    |--------------------------------------------------------------------------
    |
    | Las solicitudes de los siguientes dominios/hosts recibirán cookies de 
    | autenticación API con estado. Típicamente, estos deberían incluir tus
    | dominios locales y de producción que acceden a tu API a través de un SPA frontend.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
        // Sanctum::currentRequestHost(),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Guards de Sanctum
    |--------------------------------------------------------------------------
    |
    | Este array contiene los guards de autenticación que serán verificados cuando
    | Sanctum esté tratando de autenticar una solicitud. Si ninguno de estos guards
    | puede autenticar la solicitud, Sanctum usará el bearer token que esté
    | presente en la solicitud entrante para la autenticación.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Minutos de Expiración
    |--------------------------------------------------------------------------
    |
    | Este valor controla el número de minutos hasta que un token emitido será
    | considerado expirado. Esto sobrescribirá cualquier valor establecido en el
    | atributo "expires_at" del token, pero las sesiones de primera parte no se ven afectadas.
    |
    */

    'expiration' => 5, // 5 minutos para cumplir con los requisitos

    /*
    |--------------------------------------------------------------------------
    | Prefijo de Token
    |--------------------------------------------------------------------------
    |
    | Sanctum puede prefijar nuevos tokens para aprovechar numerosas
    | iniciativas de escaneo de seguridad mantenidas por plataformas de código abierto
    | que notifican a los desarrolladores si cometen tokens en repositorios.
    |
    | Ver: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Middleware de Sanctum
    |--------------------------------------------------------------------------
    |
    | Al autenticar tu SPA de primera parte con Sanctum, puedes necesitar
    | personalizar algunos de los middleware que Sanctum usa mientras procesa la
    | solicitud. Puedes cambiar el middleware listado abajo según sea requerido.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
