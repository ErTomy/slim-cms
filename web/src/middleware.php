<?php

use Slim\App;

return function (App $app) {

    $settings = $app->getContainer()->get('settings');
    
    $app->add(new \Tuupola\Middleware\JwtAuthentication([
        "path" => "/api", /* or ["/api", "/admin"] */
        "attribute" => "decoded_token_data",
        "secret" => $settings['jwt']['secret'],
        "algorithm" => ["HS256"],
        "secure" => false,
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]));

};
